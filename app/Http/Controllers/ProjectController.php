<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
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

        return view('projects.show', compact('project'));
    }
}
