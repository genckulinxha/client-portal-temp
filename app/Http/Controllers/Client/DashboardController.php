<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Document;
use App\Models\CaseModel;
use App\Models\CalendarEvent;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Disable query logging to prevent memory buildup
        \DB::disableQueryLog();
        
        $client = $request->client;

        // Get client's active cases (limited eager loading)
        $cases = CaseModel::where('client_id', $client->id)
            ->with(['attorney:id,name', 'paralegal:id,name'])
            ->select(['id', 'case_number', 'case_title', 'status', 'attorney_id', 'paralegal_id', 'client_id'])
            ->get();

        // Get pending tasks assigned to client
        $pendingTasks = Task::where('assigned_to_client_id', $client->id)
            ->whereIn('status', ['pending', 'in_progress'])
            ->with(['case:id,case_number', 'createdByUser:id,name'])
            ->select(['id', 'title', 'description', 'status', 'priority', 'due_date', 'case_id', 'created_by_user_id', 'assigned_to_client_id'])
            ->orderBy('due_date', 'asc')
            ->get();

        // Get completed tasks (last 5)
        $completedTasks = Task::where('assigned_to_client_id', $client->id)
            ->where('status', 'completed')
            ->with(['case:id,case_number', 'createdByUser:id,name'])
            ->select(['id', 'title', 'status', 'completed_at', 'case_id', 'created_by_user_id', 'assigned_to_client_id'])
            ->orderBy('completed_at', 'desc')
            ->limit(5)
            ->get();

        // Get recent documents
        $recentDocuments = Document::where('client_id', $client->id)
            ->where('client_viewable', true)
            ->with(['case:id,case_number', 'uploadedByUser:id,name'])
            ->select(['id', 'title', 'filename', 'created_at', 'case_id', 'uploaded_by_user_id', 'client_id'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get upcoming calendar events
        $upcomingEvents = CalendarEvent::where('client_id', $client->id)
            ->where('start_datetime', '>', now())
            ->with(['user:id,name', 'case:id,case_number'])
            ->select(['id', 'title', 'start_datetime', 'location', 'user_id', 'case_id', 'client_id'])
            ->orderBy('start_datetime', 'asc')
            ->limit(5)
            ->get();

        // Calculate progress metrics efficiently
        $taskCounts = Task::selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed
            ')
            ->where('assigned_to_client_id', $client->id)
            ->first();
            
        $totalTasks = $taskCounts->total ?? 0;
        $completedTasksCount = $taskCounts->completed ?? 0;
        $progressPercentage = $totalTasks > 0 ? round(($completedTasksCount / $totalTasks) * 100) : 0;

        // Force garbage collection before rendering view
        if (function_exists('gc_collect_cycles')) {
            gc_collect_cycles();
        }
        
        return view('client.dashboard', compact(
            'client',
            'cases',
            'pendingTasks',
            'completedTasks',
            'recentDocuments',
            'upcomingEvents',
            'progressPercentage'
        ));
    }
}