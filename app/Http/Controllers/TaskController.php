<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'status' => 'required|in:todo,in_progress,review,done',
            'files.*' => 'nullable|file|max:10240',
        ]);

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
                    'project_id' => $task->project_id,
                    'name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        return redirect()->back()->with('success', 'Task added successfully.');
    }

    public function updateStatus(Request $request, $id)
    {
        $task = Task::with('project')->findOrFail($id);
        $oldStatus = $task->status;
        
        // If status is changed to done, reset verification if it was verified
        $updateData = ['status' => $request->status];
        if ($request->status !== 'done') {
            $updateData['is_verified'] = false;
            $updateData['verified_at'] = null;
        }

        $task->update($updateData);

        \App\Models\TaskHistory::create([
            'task_id' => $task->id,
            'user_id' => Auth::id(),
            'field' => 'status',
            'old_value' => $oldStatus,
            'new_value' => $request->status,
            'action' => 'status_changed',
        ]);

        \App\Models\ClientActivity::create([
            'client_id' => $task->project->client_id,
            'provider_id' => $task->project->provider_id,
            'project_id' => $task->project->id,
            'activity_type' => 'task_status_changed',
            'description' => 'Task "' . $task->title . '" status changed from ' . $oldStatus . ' to ' . $request->status . '.',
        ]);

        return redirect()->back()->with('success', 'Task status updated.');
    }

    public function verify(Request $request, $id)
    {
        $task = Task::with('project')->findOrFail($id);

        if (Auth::id() !== $task->project->client_id) {
            abort(403);
        }

        if ($task->status !== 'done') {
            return redirect()->back()->with('error', 'Only completed tasks can be verified.');
        }

        $task->update([
            'is_verified' => true,
            'verified_at' => now(),
        ]);

        \App\Models\TaskHistory::create([
            'task_id' => $task->id,
            'user_id' => Auth::id(),
            'action' => 'verified',
        ]);

        return redirect()->back()->with('success', 'Task verified successfully.');
    }

    public function destroy($id)
    {
        Task::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Task deleted.');
    }
}
