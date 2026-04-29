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
            'files.*' => 'nullable|file|max:10240',
        ]);

        $validated['provider_id'] = Auth::id();
        
        $team = Team::where('owner_id', Auth::id())->first();
        if ($team) {
            $validated['team_id'] = $team->id;
        }

        $task = Task::create($validated);

        // Record history
        \App\Models\TaskHistory::create([
            'task_id' => $task->id,
            'user_id' => Auth::id(),
            'action' => 'created',
            'new_value' => $task->title,
        ]);

        // Handle file uploads
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('documents', 'public');
                \App\Models\Document::create([
                    'task_id' => $task->id,
                    'user_id' => Auth::id(),
                    'name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        return redirect()->back()->with('success', 'Task created successfully.');
    }

    public function show($id)
    {
        $task = Task::where('provider_id', Auth::id())
                    ->with(['assignedUser', 'documents', 'histories.user'])
                    ->findOrFail($id);
        
        return response()->json($task);
    }

    public function update(Request $request, $id)
    {
        $task = Task::where('provider_id', Auth::id())->findOrFail($id);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'priority' => 'required|in:normal,high,urgent',
            'status' => 'required|in:todo,in_progress,review,done',
            'files.*' => 'nullable|file|max:10240',
        ]);

        $oldValues = $task->only(['title', 'description', 'assigned_to', 'due_date', 'priority', 'status']);
        
        $task->update($validated);

        // Record history for changed fields
        foreach ($validated as $key => $newValue) {
            if ($key === 'files') continue;
            
            $oldValue = $oldValues[$key] ?? null;
            if ($oldValue != $newValue) {
                \App\Models\TaskHistory::create([
                    'task_id' => $task->id,
                    'user_id' => Auth::id(),
                    'field' => $key,
                    'old_value' => $oldValue,
                    'new_value' => $newValue,
                    'action' => 'updated',
                ]);
            }
        }

        // Handle additional file uploads
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('documents', 'public');
                \App\Models\Document::create([
                    'task_id' => $task->id,
                    'user_id' => Auth::id(),
                    'name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        return redirect()->back()->with('success', 'Task updated successfully.');
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:todo,in_progress,review,done',
        ]);

        $task = Task::where('provider_id', Auth::id())->findOrFail($id);
        $oldStatus = $task->status;
        $task->update(['status' => $validated['status']]);

        \App\Models\TaskHistory::create([
            'task_id' => $task->id,
            'user_id' => Auth::id(),
            'field' => 'status',
            'old_value' => $oldStatus,
            'new_value' => $validated['status'],
            'action' => 'status_changed',
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'task' => $task]);
        }
        
        return redirect()->back()->with('success', 'Task status updated.');
    }
}
