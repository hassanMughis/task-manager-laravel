@extends('layouts.app')

@section('title', 'New Task')

@section('content')
<div class="card" style="max-width:500px; margin:0 auto;">
    <h1>New Task</h1>
    <form method="POST" action="{{ route('tasks.store') }}">
        @csrf
        @include('tasks._form')
        <button type="submit">Create Task</button>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
