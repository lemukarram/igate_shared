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
        $task = Task::findOrFail($id);
        $oldStatus = $task->status;
        $task->update(['status' => $request->status]);

        \App\Models\TaskHistory::create([
            'task_id' => $task->id,
            'user_id' => Auth::id(),
            'field' => 'status',
            'old_value' => $oldStatus,
            'new_value' => $request->status,
            'action' => 'status_changed',
        ]);

        return redirect()->back()->with('success', 'Task status updated.');
    }

    public function destroy($id)
    {
        Task::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Task deleted.');
    }
}
