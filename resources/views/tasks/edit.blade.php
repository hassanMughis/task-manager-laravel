@extends('layouts.app')

@section('title', 'Edit Task')

@section('content')
<div class="card" style="max-width:500px; margin:0 auto;">
    <h1>Edit Task</h1>
    <form method="POST" action="{{ route('tasks.update', $task) }}">
        @csrf
        @method('PUT')
        @include('tasks._form')
        <button type="submit">Save Changes</button>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
