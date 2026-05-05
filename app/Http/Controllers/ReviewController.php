<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $project = Project::findOrFail($request->project_id);
        $user = Auth::user();

        // Check if user is part of the project
        if ($user->id !== $project->client_id && $user->id !== $project->provider_id) {
            abort(403);
        }

        // Check if project is completed
        if ($project->status !== 'completed') {
            return redirect()->back()->with('error', 'You can only review completed projects.');
        }

        // Determine reviewee
        $reviewee_id = ($user->id === $project->client_id) ? $project->provider_id : $project->client_id;

        // Check if review already exists
        $exists = Review::where('project_id', $project->id)
            ->where('reviewer_id', $user->id)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'You have already reviewed this project.');
        }

        Review::create([
            'project_id' => $project->id,
            'reviewer_id' => $user->id,
            'reviewee_id' => $reviewee_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return redirect()->back()->with('success', 'Review submitted successfully.');
    }
}
