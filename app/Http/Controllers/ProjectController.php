<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function show($id)
    {
        $query = Project::with(['service', 'client', 'provider', 'tasks', 'milestones', 'documents.user']);

        if (Auth::user()->role !== 'admin') {
            $query->where(function($q) {
                $q->where('client_id', Auth::id())
                  ->orWhere('provider_id', Auth::id());
            });
        }

        $project = $query->findOrFail($id);

        // Auto-populate tasks from service subtasks if none exist
        if ($project->tasks->isEmpty() && $project->service && $project->service->subtasks) {
            foreach ($project->service->subtasks as $subtask) {
                \App\Models\Task::create([
                    'project_id' => $project->id,
                    'provider_id' => $project->provider_id,
                    'title' => $subtask,
                    'status' => 'todo',
                ]);
            }
            // Refresh tasks relation
            $project->load('tasks');
        }

        $messages = Message::where('project_id', $project->id)->with('user')->oldest()->get();

        return view('projects.show', compact('project', 'messages'));
    }

    public function sendMessage(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $project = Project::findOrFail($id);

        if (Auth::user()->role !== 'admin' && Auth::id() !== $project->client_id && Auth::id() !== $project->provider_id) {
            abort(403);
        }

        Message::create([
            'project_id' => $project->id,
            'user_id' => Auth::id(),
            'message' => $request->message,
        ]);

        return redirect()->back()->with('success', 'Message sent.');
    }
}
