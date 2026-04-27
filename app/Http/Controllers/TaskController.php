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
        ]);

        Task::create($validated);

        return redirect()->back()->with('success', 'Task added successfully.');
    }

    public function updateStatus(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        $task->update(['status' => $request->status]);
        return redirect()->back()->with('success', 'Task status updated.');
    }

    public function destroy($id)
    {
        Task::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Task deleted.');
    }
}
