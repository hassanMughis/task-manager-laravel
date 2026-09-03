<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    /**
     * Show the form for creating a new task.
     */
    public function create(): View
    {
        return view('tasks.create');
    }

    /**
     * Store a newly created task for the logged-in user.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateTask($request);

        $request->user()->tasks()->create($validated);

        return redirect()->route('dashboard')->with('status', 'Task created.');
    }

    /**
     * Show the form for editing a task.
     */
    public function edit(Task $task): View
    {
        $this->authorize('update', $task);

        return view('tasks.edit', ['task' => $task]);
    }

    /**
     * Update a task owned by the logged-in user.
     */
    public function update(Request $request, Task $task): RedirectResponse
    {
        $this->authorize('update', $task);

        $validated = $this->validateTask($request);

        $task->update($validated);

        return redirect()->route('dashboard')->with('status', 'Task updated.');
    }

    /**
     * Delete a task owned by the logged-in user.
     */
    public function destroy(Task $task): RedirectResponse
    {
        $this->authorize('delete', $task);

        $task->delete();

        return redirect()->route('dashboard')->with('status', 'Task deleted.');
    }

    /**
     * Toggle a task between pending and completed.
     */
    public function toggle(Task $task): RedirectResponse
    {
        $this->authorize('update', $task);

        $task->update([
            'status' => $task->isCompleted() ? Task::STATUS_PENDING : Task::STATUS_COMPLETED,
        ]);

        return redirect()->route('dashboard');
    }

    /**
     * Shared validation rules for creating/updating a task.
     */
    private function validateTask(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', 'in:pending,completed'],
            'due_date' => ['nullable', 'date'],
        ]);
    }
}
