<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $client = $request->client;

        $tasks = Task::where('assigned_to_client_id', $client->id)
            ->with(['case', 'createdByUser', 'documents'])
            ->orderBy('due_date', 'asc')
            ->paginate(15);

        return view('client.tasks.index', compact('client', 'tasks'));
    }

    public function complete(Request $request, Task $task)
    {
        $client = $request->client;

        // Verify task belongs to this client
        if ($task->assigned_to_client_id !== $client->id) {
            abort(403, 'Unauthorized');
        }

        // Validate completion notes if provided
        $request->validate([
            'completion_notes' => 'nullable|string|max:1000',
        ]);

        $task->markCompleted($request->completion_notes);

        return redirect()->back()->with('success', 'Task marked as completed!');
    }
}