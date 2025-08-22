<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\User;
use App\Models\Task;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ClientPortalController extends Controller
{
    public function register(Request $request)
    {
        \Log::info('Client registration started', ['email' => $request->email]);
        
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'phone' => 'required|string',
            'address_line_1' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string|size:2',
            'zip_code' => 'required|string',
        ]);

        DB::beginTransaction();
        
        try {
            // Create user with client role
            $user = User::create([
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
                'password' => $request->password, // Will be hashed automatically
                'role' => 'client',
                'phone' => $request->phone,
            ]);
            \Log::info('User created successfully', ['user_id' => $user->id, 'email' => $user->email]);

            // Create corresponding client record (without password since User handles auth)
            $client = Client::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address_line_1' => $request->address_line_1,
                'address_line_2' => $request->address_line_2,
                'city' => $request->city,
                'state' => strtoupper($request->state),
                'zip_code' => $request->zip_code,
                'status' => 'prospect',
            ]);
            \Log::info('Client created successfully', ['client_id' => $client->id, 'email' => $client->email]);

            DB::commit();

            return response()->json([
                'message' => 'Registration successful',
                'user' => $user,
                'client' => $client,
                'token' => $user->createToken('client-portal')->plainTextToken,
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Client registration failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)
                   ->where('role', 'client')
                   ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Get the corresponding client record for additional data
        $client = Client::where('email', $request->email)->first();

        return response()->json([
            'user' => $user,
            'client' => $client,
            'token' => $user->createToken('client-portal')->plainTextToken,
        ]);
    }

    public function dashboard(Request $request)
    {
        $user = $request->user();
        \Log::info('Dashboard request', ['user_id' => $user->id, 'user_email' => $user->email]);
        
        $client = Client::where('email', $user->email)->first();
        
        if (!$client) {
            \Log::warning('Dashboard: Client record not found', ['user_email' => $user->email]);
            return response()->json(['message' => 'Client record not found'], 404);
        }
        
        \Log::info('Dashboard: Client found', ['client_id' => $client->id]);
        
        $totalCases = $client->cases()->count();
        $activeCases = $client->cases()->active()->count();
        
        \Log::info('Dashboard stats', [
            'client_id' => $client->id,
            'total_cases' => $totalCases,
            'active_cases' => $activeCases,
        ]);
        
        return response()->json([
            'user' => $user,
            'client' => $client,
            'stats' => [
                'total_tasks' => $client->tasks()->count(),
                'pending_tasks' => $client->tasks()->where('status', 'pending')->count(),
                'completed_tasks' => $client->tasks()->where('status', 'completed')->count(),
                'overdue_tasks' => $client->tasks()->overdue()->count(),
                'total_cases' => $totalCases,
                'active_cases' => $activeCases,
            ],
            'recent_tasks' => $client->tasks()
                ->with(['case'])
                ->latest()
                ->limit(5)
                ->get(),
        ]);
    }

    public function tasks(Request $request)
    {
        $user = $request->user();
        $client = Client::where('email', $user->email)->first();
        
        if (!$client) {
            return response()->json(['message' => 'Client record not found'], 404);
        }
        
        $tasks = $client->tasks()
            ->forClient()
            ->with(['case'])
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($request->overdue, function ($query) {
                return $query->overdue();
            })
            ->orderBy('priority', 'desc')
            ->orderBy('due_date', 'asc')
            ->paginate(20);

        return response()->json($tasks);
    }

    public function completeTask(Request $request, Task $task)
    {
        $user = $request->user();
        $client = Client::where('email', $user->email)->first();
        
        if (!$client) {
            return response()->json(['message' => 'Client record not found'], 404);
        }
        
        if ($task->client_id !== $client->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($task->task_type !== 'client_task') {
            return response()->json(['message' => 'This task cannot be completed by clients'], 400);
        }

        $task->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return response()->json([
            'message' => 'Task completed successfully',
            'task' => $task->fresh(),
        ]);
    }

    public function documents(Request $request)
    {
        $user = $request->user();
        $client = Client::where('email', $user->email)->first();
        
        if (!$client) {
            return response()->json(['message' => 'Client record not found'], 404);
        }
        
        $documents = Document::whereIn('case_id', $client->cases()->pluck('id'))
            ->where('client_accessible', true)
            ->with(['case'])
            ->when($request->category, function ($query, $category) {
                return $query->where('category', $category);
            })
            ->latest()
            ->paginate(20);

        return response()->json($documents);
    }

    public function uploadDocument(Request $request)
    {
        $request->validate([
            'case_id' => 'required|exists:cases,id',
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'file' => 'required|file|max:51200', // 50MB
        ]);

        $user = $request->user();
        $client = Client::where('email', $user->email)->first();
        
        if (!$client) {
            return response()->json(['message' => 'Client record not found'], 404);
        }
        
        // Verify client owns this case
        if (!$client->cases()->where('id', $request->case_id)->exists()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $file = $request->file('file');
        $path = $file->store('case-documents', 'local');

        $document = Document::create([
            'case_id' => $request->case_id,
            'title' => $request->title,
            'category' => $request->category,
            'file_path' => $path,
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'uploaded_by_user_id' => $user->id, // Use the authenticated user ID
            'client_accessible' => true,
        ]);

        return response()->json([
            'message' => 'Document uploaded successfully',
            'document' => $document,
        ]);
    }

    public function cases(Request $request)
    {
        $user = $request->user();
        \Log::info('Client cases request', ['user_id' => $user->id, 'user_email' => $user->email]);
        
        $client = Client::where('email', $user->email)->first();
        \Log::info('Client lookup result', ['client_found' => $client ? true : false, 'client_id' => $client?->id]);
        
        if (!$client) {
            \Log::warning('Client record not found for user', ['user_email' => $user->email]);
            return response()->json(['message' => 'Client record not found'], 404);
        }
        
        $cases = $client->cases()
            ->with(['caseType', 'assignedAttorney'])
            ->latest()
            ->get(); // Use get() instead of paginate() for debugging
            
        \Log::info('Cases query result', ['cases_count' => $cases->count(), 'client_id' => $client->id]);

        return response()->json([
            'cases' => $cases,
            'debug_info' => [
                'user_id' => $user->id,
                'client_id' => $client->id,
                'cases_count' => $cases->count()
            ]
        ]);
    }
}