<label for="title">Title</label>
<input id="title" type="text" name="title" value="{{ old('title', $task->title ?? '') }}" required autofocus>

<label for="description">Description</label>
<textarea id="description" name="description">{{ old('description', $task->description ?? '') }}</textarea>

<label for="due_date">Due date</label>
<input id="due_date" type="date" name="due_date" value="{{ old('due_date', optional($task->due_date ?? null)->format('Y-m-d')) }}">

<label for="status">Status</label>
<select id="status" name="status">
    <option value="pending" {{ old('status', $task->status ?? 'pending') === 'pending' ? 'selected' : '' }}>Pending</option>
    <option value="completed" {{ old('status', $task->status ?? 'pending') === 'completed' ? 'selected' : '' }}>Completed</option>
</select>
