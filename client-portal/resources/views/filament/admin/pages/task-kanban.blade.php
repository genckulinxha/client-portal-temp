<x-filament-panels::page>
    <div class="space-y-4" x-data>
        <!-- Filters -->
        <div class="flex flex-wrap items-end gap-4">
            <div>
                <label class="fi-fo-field-wrp-label inline-flex items-center gap-x-3">
                    <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">Case</span>
                </label>
                <select wire:model.live="caseId" class="fi-select-input block w-64 border-none bg-white shadow-sm ring-1 ring-gray-950/10 transition duration-75 placeholder:text-gray-400 focus:ring-2 focus:ring-primary-600 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.400)] sm:text-sm sm:leading-6 [&_optgroup]:bg-white [&_optgroup]:text-gray-950 [&_option]:bg-white [&_option]:text-gray-950 rounded-lg dark:bg-white/5 dark:text-white dark:ring-white/20 dark:focus:ring-primary-500 dark:[&_optgroup]:bg-gray-900 dark:[&_optgroup]:text-white dark:[&_option]:bg-gray-900 dark:[&_option]:text-white">
                    <option value="">All Cases</option>
                    @foreach(\App\Models\CaseModel::query()->orderBy('id','desc')->limit(200)->get() as $case)
                        <option value="{{ $case->id }}">{{ $case->case_number ?? ('Case #' . $case->id) }} - {{ $case->case_title ?? $case->title ?? '' }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="fi-fo-field-wrp-label inline-flex items-center gap-x-3">
                    <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">Priority</span>
                </label>
                <select wire:model.live="priority" class="fi-select-input block w-48 border-none bg-white shadow-sm ring-1 ring-gray-950/10 transition duration-75 placeholder:text-gray-400 focus:ring-2 focus:ring-primary-600 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.400)] sm:text-sm sm:leading-6 [&_optgroup]:bg-white [&_optgroup]:text-gray-950 [&_option]:bg-white [&_option]:text-gray-950 rounded-lg dark:bg-white/5 dark:text-white dark:ring-white/20 dark:focus:ring-primary-500 dark:[&_optgroup]:bg-gray-900 dark:[&_optgroup]:text-white dark:[&_option]:bg-gray-900 dark:[&_option]:text-white">
                    <option value="">All Priorities</option>
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                    <option value="urgent">Urgent</option>
                </select>
            </div>
            <div>
                <label class="fi-fo-field-wrp-label inline-flex items-center gap-x-3">
                    <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">Due</span>
                </label>
                <select wire:model.live="dueWindow" class="fi-select-input block w-40 border-none bg-white shadow-sm ring-1 ring-gray-950/10 transition duration-75 placeholder:text-gray-400 focus:ring-2 focus:ring-primary-600 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.400)] sm:text-sm sm:leading-6 [&_optgroup]:bg-white [&_optgroup]:text-gray-950 [&_option]:bg-white [&_option]:text-gray-950 rounded-lg dark:bg-white/5 dark:text-white dark:ring-white/20 dark:focus:ring-primary-500 dark:[&_optgroup]:bg-gray-900 dark:[&_optgroup]:text-white dark:[&_option]:bg-gray-900 dark:[&_option]:text-white">
                    <option value="">Any Due Date</option>
                    <option value="overdue">Overdue</option>
                </select>
            </div>
        </div>

        <!-- Board -->
        <div class="flex gap-6 overflow-x-auto pb-4">
            @php
                $groups = $this->tasksByStatus;
                $statuses = [
                    'pending' => 'Pending',
                    'in_progress' => 'In Progress',
                    'completed' => 'Completed',
                    'cancelled' => 'Cancelled',
                ];
            @endphp

            @foreach ($statuses as $statusKey => $statusLabel)
                <div class="flex-shrink-0 w-72 rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-800">
                    <div class="p-4 pb-0">
                        <div class="mb-4 flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $statusLabel }}</h3>
                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600 dark:bg-gray-700 dark:text-gray-300">{{ ($groups[$statusKey] ?? collect())->count() }}</span>
                        </div>
                    </div>
                    <div id="col-{{ $statusKey }}" data-status="{{ $statusKey }}" class="kanban-column min-h-[400px] space-y-3 p-4 pt-0">
                        @foreach (($groups[$statusKey] ?? collect()) as $task)
                            <div class="kanban-card rounded-md border border-gray-200 bg-white p-3 shadow-sm hover:border-gray-300 hover:bg-gray-50 cursor-move select-none dark:border-gray-600 dark:bg-gray-700 dark:hover:border-gray-500 dark:hover:bg-gray-600" data-task-id="{{ $task->id }}">
                                <a href="{{ \App\Filament\Admin\Resources\TaskResource::getUrl('edit', ['record' => $task]) }}" class="block select-none">
                                    <div class="mb-2">
                                        <div class="text-sm font-medium text-gray-900 select-none dark:text-white">{{ $task->title }}</div>
                                        @if($task->case)
                                            <div class="text-xs text-gray-500 select-none dark:text-gray-400">{{ $task->case->case_number }} - {{ $task->case->case_title }}</div>
                                        @else
                                            <div class="text-xs text-gray-500 select-none dark:text-gray-400">No Case</div>
                                        @endif
                                    </div>
                                    @if($task->due_date)
                                        <div class="text-xs {{ $task->is_overdue ? 'text-red-500 font-semibold dark:text-red-400' : 'text-gray-600 dark:text-gray-300' }} select-none">
                                            Due: {{ $task->due_date->format('M d, Y') }}
                                        </div>
                                    @endif
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <style>
        .sortable-ghost {
            opacity: 0.4;
            background: rgba(59, 130, 246, 0.1) !important;
            border: 2px dashed #3b82f6 !important;
        }
        
        .sortable-chosen {
            transform: rotate(2deg);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        
        .dark .sortable-chosen {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }
        
        .sortable-drag {
            transform: rotate(5deg);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }
        
        .dark .sortable-drag {
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
        }
        
        .kanban-card {
            transition: all 0.2s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }
        
        .kanban-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        }
        
        .dark .kanban-card:hover {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .kanban-column {
            transition: all 0.2s ease;
            border-radius: 0.5rem;
        }
        
        .kanban-column.drag-active {
            background-color: rgba(59, 130, 246, 0.02);
        }
        
        .dark .kanban-column.drag-active {
            background-color: rgba(55, 65, 81, 0.05);
        }
        
        .kanban-column.drag-over {
            background-color: rgba(59, 130, 246, 0.05);
        }
        
        .dark .kanban-column.drag-over {
            background-color: rgba(59, 130, 246, 0.05);
        }
        
    </style>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            const statuses = ['pending','in_progress','completed','cancelled'];


            statuses.forEach((status) => {
                const columnElement = document.getElementById(`col-${status}`);
                
                new Sortable(columnElement, {
                    group: 'tasks',
                    animation: 200,
                    easing: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)',
                    ghostClass: 'sortable-ghost',
                    chosenClass: 'sortable-chosen',
                    dragClass: 'sortable-drag',
                    forceFallback: true,
                    fallbackClass: 'cursor-grabbing',
                    fallbackOnBody: true,
                    swapThreshold: 0.65,
                    invertSwap: true,
                    direction: 'vertical',
                    onStart: (evt) => {
                        document.querySelectorAll('.kanban-column').forEach(col => {
                            col.style.transition = 'all 0.2s ease';
                            col.classList.add('drag-active');
                        });
                    },
                    onMove: (evt) => {
                        // Allow dropping on empty columns or between cards
                        return evt.related.className.indexOf('kanban-card') !== -1 || 
                               evt.to.classList.contains('kanban-column');
                    },
                    onEnd: (evt) => {
                        // Remove all drag-over and drag-active classes
                        document.querySelectorAll('.kanban-column').forEach(col => {
                            col.classList.remove('drag-over', 'drag-active');
                            col.style.transition = '';
                        });
                        
                        const taskId = evt.item.dataset.taskId;
                        const newStatus = evt.to.dataset.status;
                        const newIndex = evt.newIndex;
                        const page = document.querySelector('[wire\\:id]');
                        
                        if (!page) return;
                        
                        // Show loading state
                        evt.item.style.opacity = '0.7';
                        evt.item.style.transition = 'opacity 0.2s ease';
                        
                        window.Livewire.find(page.getAttribute('wire:id'))
                            .call('moveTask', Number(taskId), newStatus, Number.isInteger(newIndex) ? newIndex : null)
                            .then(() => {
                                evt.item.style.opacity = '';
                                evt.item.style.transition = '';
                            })
                            .catch((error) => {
                                console.error('Error moving task:', error);
                                evt.item.style.opacity = '';
                                evt.item.style.transition = '';
                            });
                    },
                    onAdd: (evt) => {
                        evt.to.classList.add('drag-over');
                        setTimeout(() => {
                            evt.to.classList.remove('drag-over');
                        }, 300);
                    },
                    onRemove: (evt) => {
                        evt.from.classList.remove('drag-over');
                    },
                });
            });

        });
    </script>
</x-filament-panels::page> 