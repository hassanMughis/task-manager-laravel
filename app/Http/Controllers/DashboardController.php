<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the logged-in user's dashboard: a list of their tasks.
     */
    public function index(Request $request): View
    {
        $tasks = $request->user()
            ->tasks()
            ->orderBy('status')
            ->orderByRaw('due_date IS NULL, due_date asc')
            ->paginate(10);

        return view('dashboard', [
            'tasks' => $tasks,
            'pendingCount' => $request->user()->tasks()->where('status', 'pending')->count(),
            'completedCount' => $request->user()->tasks()->where('status', 'completed')->count(),
        ]);
    }
}
