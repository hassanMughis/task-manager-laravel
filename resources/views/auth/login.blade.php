@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="card" style="max-width:420px; margin:0 auto;">
    <h1></h1>
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <label for="email">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>

        <label for="password">Password</label>
        <input id="password" type="password" name="password" required>

        <label>
            <input type="checkbox" name="remember" style="width:auto; display:inline-block;"> Remember me
        </label>

        <button type="submit">Sign up</button>
    </form>
    <p style="margin-top:1rem;">Don't have an account? <a href="{{ route('register') }}">Register</a></p>
</div>
@endsection
