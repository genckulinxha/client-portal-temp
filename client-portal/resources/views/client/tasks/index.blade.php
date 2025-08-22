@extends('client.layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">My Tasks</h1>
        <p class="mt-2 text-gray-600">Track and complete your assigned tasks.</p>
    </div>

    <!-- Task List -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        @if($tasks->count() > 0)
            <ul class="divide-y divide-gray-200">
                @foreach($tasks as $task)
                    <li class="px-6 py-4 {{ $task->is_overdue ? 'bg-red-50' : '' }}">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-gray-900">{{ $task->title }}</p>
                                    <div class="flex items-center space-x-2">
                                        <!-- Priority Badge -->
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $task->priority === 'urgent' ? 'bg-red-100 text-red-800' : '' }}
                                            {{ $task->priority === 'high' ? 'bg-orange-100 text-orange-800' : '' }}
                                            {{ $task->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $task->priority === 'low' ? 'bg-gray-100 text-gray-800' : '' }}">
                                            {{ ucfirst($task->priority) }}
                                        </span>
                                        
                                        <!-- Status Badge -->
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $task->status === 'pending' ? 'bg-gray-100 text-gray-800' : '' }}
                                            {{ $task->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $task->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $task->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                        </span>
                                    </div>
                                </div>
                                
                                <p class="mt-1 text-sm text-gray-600">{{ $task->description }}</p>
                                
                                <!-- Task Meta Information -->
                                <div class="mt-2 flex items-center text-xs text-gray-500 space-x-4">
                                    @if($task->case)
                                        <span class="flex items-center">
                                            <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Case: {{ $task->case->case_number }}
                                        </span>
                                    @endif
                                    
                                    @if($task->due_date)
                                        <span class="flex items-center {{ $task->is_overdue ? 'text-red-600' : '' }}">
                                            <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                            </svg>
                                            Due: {{ $task->due_date->format('M j, Y g:i A') }}
                                            @if($task->is_overdue)
                                                <span class="ml-1 font-medium">(Overdue)</span>
                                            @endif
                                        </span>
                                    @endif
                                    
                                    @if($task->createdByUser)
                                        <span class="flex items-center">
                                            <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                            </svg>
                                            Assigned by: {{ $task->createdByUser->name }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Task Requirements -->
                                @if($task->requirements && count($task->requirements) > 0)
                                    <div class="mt-3 p-3 bg-blue-50 rounded-md">
                                        <h4 class="text-sm font-medium text-blue-900">Requirements:</h4>
                                        <ul class="mt-1 text-sm text-blue-800">
                                            @foreach($task->requirements as $key => $value)
                                                <li>• {{ ucfirst(str_replace('_', ' ', $key)) }}: 
                                                    @if(is_array($value))
                                                        {{ implode(', ', $value) }}
                                                    @else
                                                        {{ $value }}
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <!-- Completion Notes (if completed) -->
                                @if($task->status === 'completed' && $task->completion_notes)
                                    <div class="mt-3 p-3 bg-green-50 rounded-md">
                                        <h4 class="text-sm font-medium text-green-900">Completion Notes:</h4>
                                        <p class="mt-1 text-sm text-green-800">{{ $task->completion_notes }}</p>
                                        <p class="mt-1 text-xs text-green-600">Completed: {{ $task->completed_at ? $task->completed_at->format('M j, Y g:i A') : 'Date not recorded' }}</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Actions -->
                            @if($task->status !== 'completed' && $task->status !== 'cancelled')
                                <div class="ml-4 flex-shrink-0">
                                    <button onclick="completeTask({{ $task->id }})" 
                                            class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm font-medium">
                                        Mark Complete
                                    </button>
                                </div>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $tasks->links() }}
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No tasks</h3>
                <p class="mt-1 text-sm text-gray-500">You don't have any tasks assigned at this time.</p>
            </div>
        @endif
    </div>
</div>

<!-- Complete Task Modal -->
<div id="completeTaskModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="completeTaskForm" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                        Complete Task
                    </h3>
                    <div class="mt-4">
                        <label for="completion_notes" class="block text-sm font-medium text-gray-700">
                            Completion Notes (Optional)
                        </label>
                        <textarea id="completion_notes" name="completion_notes" rows="3" 
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                  placeholder="Add any notes about how you completed this task..."></textarea>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Complete Task
                    </button>
                    <button type="button" onclick="closeCompleteModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function completeTask(taskId) {
    document.getElementById('completeTaskForm').action = '/client/tasks/' + taskId + '/complete';
    document.getElementById('completeTaskModal').classList.remove('hidden');
}

function closeCompleteModal() {
    document.getElementById('completeTaskModal').classList.add('hidden');
    document.getElementById('completion_notes').value = '';
}

// Close modal when clicking outside
document.getElementById('completeTaskModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeCompleteModal();
    }
});
</script>
@endsection