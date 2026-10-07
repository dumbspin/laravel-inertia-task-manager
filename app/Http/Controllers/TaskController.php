<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class TaskController extends Controller
{
    /**
     * Show the task dashboard: statistics, search, filters and the task list.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();

        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', Rule::in(array_keys(Task::STATUSES))],
            'priority' => ['nullable', Rule::in(array_keys(Task::PRIORITIES))],
        ], [], [
            'search' => 'search term',
            'status' => 'status',
            'priority' => 'priority',
        ]);

        $tasks = $user->tasks()
            ->search($filters['search'] ?? null)
            ->status($filters['status'] ?? null)
            ->priority($filters['priority'] ?? null)
            ->with('user:id,name')
            ->latest('created_at')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Tasks/Index', [
            'tasks' => $tasks,
            'filters' => [
                'search' => $filters['search'] ?? '',
                'status' => $filters['status'] ?? '',
                'priority' => $filters['priority'] ?? '',
            ],
            'stats' => $this->stats($user->tasks()),
            'statuses' => Task::STATUSES,
            'priorities' => Task::PRIORITIES,
        ]);
    }

    /**
     * Show the form to create a task.
     */
    public function create(): Response
    {
        return Inertia::render('Tasks/Create', [
            'statuses' => Task::STATUSES,
            'priorities' => Task::PRIORITIES,
        ]);
    }

    /**
     * Persist a new task for the authenticated user.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->user()->tasks()->create($this->validated($request));

        return redirect()
            ->route('dashboard')
            ->with('success', 'Task created successfully.');
    }

    /**
     * Show the form to edit an existing task.
     */
    public function edit(Request $request, Task $task): Response
    {
        $this->authorizeTask($request, $task);

        return Inertia::render('Tasks/Edit', [
            'task' => [
                ...$task->only(['id', 'title', 'description', 'status', 'priority']),
                'due_date' => $task->formatted_due_date,
            ],
            'statuses' => Task::STATUSES,
            'priorities' => Task::PRIORITIES,
        ]);
    }

    /**
     * Update the given task.
     */
    public function update(Request $request, Task $task): RedirectResponse
    {
        $this->authorizeTask($request, $task);

        $task->update($this->validated($request));

        return redirect()
            ->route('dashboard')
            ->with('success', 'Task updated successfully.');
    }

    /**
     * Toggle the given task between completed and pending.
     */
    public function toggle(Request $request, Task $task): RedirectResponse
    {
        $this->authorizeTask($request, $task);

        $task->update([
            'status' => $task->status === 'completed' ? 'pending' : 'completed',
        ]);

        return back()->with('success', $task->status === 'completed'
            ? 'Task marked as completed.'
            : 'Task reopened as pending.');
    }

    /**
     * Delete the given task.
     */
    public function destroy(Request $request, Task $task): RedirectResponse
    {
        $this->authorizeTask($request, $task);

        $task->delete();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Task deleted successfully.');
    }

    /**
     * Validate an incoming task payload.
     *
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'min:1', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', Rule::in(array_keys(Task::STATUSES))],
            'priority' => ['required', Rule::in(array_keys(Task::PRIORITIES))],
            'due_date' => ['required', 'date'],
        ], [
            'title.required' => 'The task title cannot be empty.',
            'title.max' => 'The task title may not be greater than 255 characters.',
            'status.in' => 'The status must be one of: Pending, In Progress, Completed.',
            'priority.in' => 'The priority must be one of: Low, Medium, High.',
            'due_date.date' => 'The due date must be a valid date.',
        ]);
    }

    /**
     * Abort with a 404 when the task does not belong to the user.
     */
    private function authorizeTask(Request $request, Task $task): void
    {
        abort_unless($task->user_id === $request->user()->id, 404);
    }

    /**
     * Build the dashboard counters.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<Task>  $base
     * @return array<string, int>
     */
    private function stats($base): array
    {
        return [
            'total' => (clone $base)->count(),
            'pending' => (clone $base)->where('status', 'pending')->count(),
            'in_progress' => (clone $base)->where('status', 'in_progress')->count(),
            'completed' => (clone $base)->where('status', 'completed')->count(),
        ];
    }
}
