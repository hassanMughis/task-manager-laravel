<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Task Manager')</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            background: #f4f5f7;
            color: #222;
            margin: 0;
        }
        nav {
            background: #2d3748;
            color: #fff;
            padding: 0.75rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        nav a { color: #fff; text-decoration: none; margin-right: 1rem; }
        nav a:hover { text-decoration: underline; }
        .container { max-width: 800px; margin: 2rem auto; padding: 0 1rem; }
        .card { background: #fff; border: 1px solid #e2e8f0; border-radius: 6px; padding: 1.5rem; margin-bottom: 1.5rem; }
        h1 { font-size: 1.5rem; }
        label { display: block; margin-top: 1rem; font-weight: 600; }
        input[type=text], input[type=email], input[type=password], input[type=date], textarea, select {
            width: 100%; padding: 0.5rem; margin-top: 0.25rem; border: 1px solid #cbd5e0; border-radius: 4px; font-size: 1rem;
        }
        textarea { min-height: 80px; }
        button, .btn {
            display: inline-block; margin-top: 1rem; padding: 0.5rem 1rem; background: #3182ce; color: #fff;
            border: none; border-radius: 4px; cursor: pointer; font-size: 1rem; text-decoration: none;
        }
        button:hover, .btn:hover { background: #2c5282; }
        .btn-danger { background: #e53e3e; }
        .btn-danger:hover { background: #c53030; }
        .btn-secondary { background: #718096; }
        .btn-secondary:hover { background: #4a5568; }
        .errors { background: #fff5f5; border: 1px solid #feb2b2; color: #c53030; padding: 1rem; border-radius: 4px; margin-top: 1rem; }
        .status { background: #f0fff4; border: 1px solid #9ae6b4; color: #276749; padding: 0.75rem 1rem; border-radius: 4px; margin-bottom: 1rem; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { text-align: left; padding: 0.5rem; border-bottom: 1px solid #e2e8f0; }
        .badge { padding: 0.15rem 0.5rem; border-radius: 3px; font-size: 0.85rem; }
        .badge-pending { background: #fefcbf; color: #744210; }
        .badge-completed { background: #c6f6d5; color: #22543d; }
        .actions form { display: inline; }
        .stats { display: flex; gap: 1.5rem; margin-bottom: 1rem; }
        .stats div { background: #edf2f7; padding: 0.75rem 1rem; border-radius: 4px; }
    </style>
</head>
<body>
    @auth
        <nav>
            <div>
                <a href="{{ route('dashboard') }}">Task Manager</a>
            </div>
            <div>
                <span style="margin-right:1rem;">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" style="margin-top:0;">Logout</button>
                </form>
            </div>
        </nav>
    @endauth

    <div class="container">
        @if (session('status'))
            <div class="status">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="errors">
                <ul style="margin:0; padding-left:1.2rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>
</body>
</html>
