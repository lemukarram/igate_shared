<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function show($id)
    {
        $project = Project::with(['service', 'client', 'provider', 'tasks', 'milestones', 'documents.user'])
            ->where(function($query) {
                $query->where('client_id', Auth::id())
                      ->orWhere('provider_id', Auth::id());
            })
            ->findOrFail($id);

        return view('projects.show', compact('project'));
    }
}
