<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Team;
use Illuminate\Support\Facades\Auth;

class TeamTaskController extends Controller
{
    public function index()
    {
        $query = Task::where('provider_id', Auth::id())
                     ->whereNull('project_id')
                     ->with('assignedUser');

        $tasks = $query->get()->groupBy('status');
        
        $team = Team::where('owner_id', Auth::id())->first();
        $teamMembers = $team ? $team->members()->with('user')->get() : collect();

        return view('provider.team_tasks', compact('tasks', 'teamMembers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'priority' => 'required|in:normal,high,urgent',
            'status' => 'required|in:todo,in_progress,review,done',
        ]);

        $validated['provider_id'] = Auth::id();
        
        // If team id is relevant, add it:
        $team = Team::where('owner_id', Auth::id())->first();
        if ($team) {
            $validated['team_id'] = $team->id;
        }

        Task::create($validated);

        return redirect()->back()->with('success', 'Task created successfully.');
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:todo,in_progress,review,done',
        ]);

        $task = Task::where('provider_id', Auth::id())->findOrFail($id);
        $task->update(['status' => $validated['status']]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'task' => $task]);
        }
        
        return redirect()->back()->with('success', 'Task status updated.');
    }
}
