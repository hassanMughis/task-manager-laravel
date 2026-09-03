@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="card" style="max-width:420px; margin:0 auto;">
    <h1>Create an account</h1>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <label for="name">Name</label>
        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus>

        <label for="email">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required>

        <label for="password">Password</label>
        <input id="password" type="password" name="password" required>

        <label for="password_confirmation">Confirm password</label>
        <input id="password_confirmation" type="password" name="password_confirmation" required>

        <button type="submit">Register</button>
    </form>
    <p style="margin-top:1rem;">Already have an account? <a href="{{ route('login') }}">Log in</a></p>
</div>
@endsection
