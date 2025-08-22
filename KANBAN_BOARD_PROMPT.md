### Jira-style Kanban Board Integration Prompt (Filament v3 + Laravel 12)

You are integrating a Jira-style Kanban board (draggable cards between columns) into our Laravel + Filament admin portal so attorneys can manage their case tasks.

## Project context (exact)
- **Backend**: Laravel 12, PHP 8.2; Livewire v3; Tailwind + Vite + AlpineJS
- **Admin UI**: Filament v3.3
- **Admin provider**: `app/Providers/Filament/AdminPanelProvider.php` (auto-discovers `app/Filament/Admin/Pages`, `.../Resources`, `.../Widgets`)
- **Example custom page pattern**: `app/Filament/Admin/Pages/Calendar.php` → view `resources/views/filament/admin/pages/calendar.blade.php`
- **Models**:
  - `app/Models/Task.php`
    - fillable: `title, description, type, status, priority, assigned_to_user_id, assigned_to_client_id, case_id, created_by_user_id, due_date, completed_at, requirements, completion_notes`
    - casts: `due_date`, `completed_at` datetime; `requirements` array
    - relations: `assignedToUser()`, `assignedToClient()`, `case()`, `createdByUser()`
  - `app/Models/CaseModel.php` (has `tasks()`; `attorney_id` foreign key exists)
  - `app/Models/User.php` (roles include `attorney`; helpers like `isAttorney()`)
- **Task statuses (from migration)**: `pending`, `in_progress`, `completed`, `cancelled` (`database/migrations/2025_07_27_075215_create_tasks_table.php`)
- **Existing Task resource (admin)**: `app/Filament/Admin/Resources/TaskResource.php` with pages `ListTasks`, `CreateTask`, `EditTask`

## Goal
- Add a Jira-style Kanban board in the Admin (attorney) portal showing tasks in columns by status: Pending, In Progress, Completed, Cancelled.
- Default scope: only tasks assigned to the logged-in attorney (`tasks.assigned_to_user_id = auth()->id()`).
- Filter by case, priority, and due window (e.g., Overdue).
- Drag-and-drop between columns updates the task’s `status` immediately.
- Card shows title, case number/title, due date (overdue highlight), priority; click opens existing `TaskResource` edit page.
- Optional: persist column order with `board_order` integer; otherwise sort by `due_date` asc.

## Deliverables
- New Filament Page: `app/Filament/Admin/Pages/TaskKanban.php`
- Blade view: `resources/views/filament/admin/pages/task-kanban.blade.php`
- Livewire/Filament actions to load, filter, and update tasks on drag-drop
- Optional migration for `board_order`

## Implementation details

### 1) Page class
Create `app/Filament/Admin/Pages/TaskKanban.php` extending `Filament\Pages\Page`.

- Set:
  - `protected static ?string $navigationGroup = 'Task Management';`
  - `protected static ?string $navigationLabel = 'Kanban Board';`
  - `protected static ?string $navigationIcon = 'heroicon-o-view-columns';`
  - `protected static string $view = 'filament.admin.pages.task-kanban';`
- Public state and actions:
  - `public array $columns = ['pending','in_progress','completed','cancelled'];`
  - Filters: `public ?int $caseId = null; public ?string $priority = null; public ?string $dueWindow = null;`
  - Computed getter to load tasks grouped by status using the exact query below.
  - Livewire action `moveTask(int $taskId, string $newStatus, ?int $newOrder = null)` that updates DB (respecting attorney scoping).

Query to load tasks (use exactly these conditions):

```php
Task::query()
  ->with(['case', 'assignedToUser'])
  ->when(auth()->user()->isAttorney(), fn ($q) => $q->where('assigned_to_user_id', auth()->id()))
  ->when($this->caseId, fn ($q) => $q->where('case_id', $this->caseId))
  ->when($this->priority, fn ($q) => $q->where('priority', $this->priority))
  ->when($this->dueWindow === 'overdue', fn ($q) => $q->where('due_date', '<', now())->whereNotIn('status', ['completed','cancelled']))
  ->orderBy('due_date', 'asc')
  ->get()
  ->groupBy('status');
```

Livewire action:

```php
public function moveTask(int $taskId, string $newStatus, ?int $newOrder = null): void
{
    abort_unless(in_array($newStatus, ['pending','in_progress','completed','cancelled'], true), 422);

    $task = Task::query()
        ->when(auth()->user()->isAttorney(), fn ($q) => $q->where('assigned_to_user_id', auth()->id()))
        ->findOrFail($taskId);

    $task->status = $newStatus;

    // If you implement board_order:
    // if (! is_null($newOrder)) { $task->board_order = $newOrder; }

    $task->save();
    $this->dispatch('notify', type: 'success', message: 'Task updated');
}
```

### 2) Blade view
Create `resources/views/filament/admin/pages/task-kanban.blade.php`.

- Wrap with `<x-filament-panels::page>` (same as calendar page).
- Top filter bar: case select (from `CaseModel`), priority select, due window select.
- Four columns laid out with Tailwind; each column has a container with `data-status="pending|in_progress|completed|cancelled"` and `id="col-<status>"`. Each card has `data-task-id` and a link to the `TaskResource` edit page.
- Include SortableJS (CDN or via Vite). On drop, call the Livewire method.

JS snippet (inline or via Vite):

```html
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
document.addEventListener('alpine:init', () => {
  const statuses = ['pending','in_progress','completed','cancelled'];

  statuses.forEach((status) => {
    new Sortable(document.getElementById(`col-${status}`), {
      group: 'tasks',
      animation: 150,
      onEnd: (evt) => {
        const taskId = evt.item.dataset.taskId;
        const newStatus = evt.to.dataset.status;
        const newIndex = evt.newIndex;
        // Livewire v3: call server method on the page component
        const page = document.querySelector('[wire\\:id]');
        if (!page) return;
        window.Livewire.find(page.getAttribute('wire:id'))
          .call('moveTask', Number(taskId), newStatus, Number.isInteger(newIndex) ? newIndex : null);
      },
    });
  });
});
</script>
```

### 3) Card UI and linking
- Show: Title; Case (`{{ $task->case?->case_number }} - {{ $task->case?->case_title }}`); Due date (red if overdue using `$task->is_overdue`); Priority badge with colors consistent with `TaskResource`.
- Link to edit:

```php
<a href="{{ \App\Filament\Admin\Resources\TaskResource::getUrl('edit', ['record' => $task]) }}">Edit</a>
```

### 4) Optional: persist column order
- Create migration `database/migrations/xxxx_xx_xx_xxxxxx_add_board_order_to_tasks.php` to add nullable integer `board_order` with index.
- When moving within the same status, update `board_order` via `newOrder`; default sort within columns by `board_order` then `due_date`.

## Acceptance criteria
- Page appears in Admin under “Task Management” as “Kanban Board”.
- Logging in as an `attorney` shows only their tasks by default.
- Dragging a card between columns updates `status` in DB immediately.
- Filters work and re-render columns.
- Clicking a card opens the existing `TaskResource` edit page.
- Overdue tasks are visually highlighted.

## Constraints
- Use the exact statuses: `pending`, `in_progress`, `completed`, `cancelled`.
- Do not change existing enums or model names/paths.
- Keep styles consistent with Filament/Tailwind; use Alpine + SortableJS for DnD.

## What to return
- New files `app/Filament/Admin/Pages/TaskKanban.php` and `resources/views/filament/admin/pages/task-kanban.blade.php`.
- Optional migration for `board_order` if implemented.
- Any minimal JS you added (inline or Vite).
- No other structural changes.

If anything is unclear, ask before changing enums or model fields. 