<?php

namespace App\Filament\Admin\Pages;

use App\Models\Task;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class TaskKanban extends Page
{
    protected static ?string $navigationGroup = 'Task Management';

    protected static ?string $navigationLabel = 'Kanban Board';

    protected static ?string $navigationIcon = 'heroicon-o-view-columns';

    protected static string $view = 'filament.admin.pages.task-kanban';

    /**
     * Columns shown on the board.
     *
     * @var array<int, string>
     */
    public array $columns = ['pending', 'in_progress', 'completed', 'cancelled'];

    public ?int $caseId = null;

    public ?string $priority = null;

    public ?string $dueWindow = null;

    /**
     * Load tasks grouped by status with filtering applied.
     */
    public function getTasksByStatusProperty(): Collection
    {
        return Task::query()
            ->with(['case', 'assignedToUser'])
            ->when(auth()->user()->isAttorney(), fn ($q) => $q->where('assigned_to_user_id', auth()->id()))
            ->when($this->caseId, fn ($q) => $q->where('case_id', $this->caseId))
            ->when($this->priority, fn ($q) => $q->where('priority', $this->priority))
            ->when($this->dueWindow === 'overdue', fn ($q) => $q->where('due_date', '<', now())->whereNotIn('status', ['completed','cancelled']))
            ->orderBy('board_order', 'asc')
            ->orderBy('due_date', 'asc')
            ->get()
            ->groupBy('status');
    }

    /**
     * Drag-and-drop action to update a task's status (and optionally order).
     */
    public function moveTask(int $taskId, string $newStatus, ?int $newOrder = null): void
    {
        abort_unless(in_array($newStatus, ['pending','in_progress','completed','cancelled'], true), 422);

        $task = Task::query()
            ->when(auth()->user()->isAttorney(), fn ($q) => $q->where('assigned_to_user_id', auth()->id()))
            ->findOrFail($taskId);

        $oldStatus = $task->status;
        $task->status = $newStatus;

        // Handle ordering within columns
        if (!is_null($newOrder)) {
            // Get all tasks in the new status column
            $tasksInColumn = Task::query()
                ->where('status', $newStatus)
                ->when(auth()->user()->isAttorney(), fn ($q) => $q->where('assigned_to_user_id', auth()->id()))
                ->when($this->caseId, fn ($q) => $q->where('case_id', $this->caseId))
                ->when($this->priority, fn ($q) => $q->where('priority', $this->priority))
                ->when($this->dueWindow === 'overdue', fn ($q) => $q->where('due_date', '<', now())->whereNotIn('status', ['completed','cancelled']))
                ->where('id', '!=', $taskId)
                ->orderBy('board_order', 'asc')
                ->get();

            // Reorder tasks
            $tasks = $tasksInColumn->toArray();
            array_splice($tasks, $newOrder, 0, [$task->toArray()]);

            // Update board_order for all affected tasks
            foreach ($tasks as $index => $taskData) {
                Task::where('id', $taskData['id'])->update(['board_order' => $index]);
            }
        }

        $task->save();

        $this->dispatch('notify', type: 'success', message: 'Task updated');
    }
} 