@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="card">
    <h1>My Tasks</h1>

    <div class="stats">
        <div><strong>{{ $pendingCount }}</strong> pending</div>
        <div><strong>{{ $completedCount }}</strong> completed</div>
    </div>

    <a href="{{ route('tasks.create') }}" class="btn">+ New Task</a>

    @if ($tasks->isEmpty())
        <p style="margin-top:1rem;">You don't have any tasks yet.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Due date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tasks as $task)
                    <tr>
                        <td>
                            <strong>{{ $task->title }}</strong>
                            @if ($task->description)
                                <div style="color:#718096; font-size:0.9rem;">{{ Str::limit($task->description, 80) }}</div>
                            @endif
                        </td>
                        <td>{{ optional($task->due_date)->format('Y-m-d') ?? '—' }}</td>
                        <td>
                            <span class="badge {{ $task->isCompleted() ? 'badge-completed' : 'badge-pending' }}">
                                {{ ucfirst($task->status) }}
                            </span>
                        </td>
                        <td class="actions">
                            <form method="POST" action="{{ route('tasks.toggle', $task) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn-secondary" style="margin-top:0;">
                                    {{ $task->isCompleted() ? 'Mark pending' : 'Mark complete' }}
                                </button>
                            </form>
                            <a href="{{ route('tasks.edit', $task) }}" class="btn" style="margin-top:0;">Edit</a>
                            <form method="POST" action="{{ route('tasks.destroy', $task) }}" onsubmit="return confirm('Delete this task?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger" style="margin-top:0;">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top:1rem;">
            {{ $tasks->links() }}
        </div>
    @endif
</div>
@endsection
