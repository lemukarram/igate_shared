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

    public function updateStatus(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        
        if (Auth::user()->role !== 'admin' && Auth::id() !== $project->provider_id) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:active,inactive,pending',
        ]);

        $oldStatus = $project->status;
        $project->update(['status' => $request->status]);

        \App\Models\ProjectHistory::create([
            'project_id' => $project->id,
            'user_id' => Auth::id(),
            'action' => 'status_updated',
            'description' => 'Project status changed from ' . $oldStatus . ' to ' . $request->status . '.',
            'metadata' => ['old' => $oldStatus, 'new' => $request->status]
        ]);

        return redirect()->back()->with('success', 'Project status updated successfully.');
    }

    public function complete($id)
    {
        $project = Project::findOrFail($id);
        
        if (Auth::id() !== $project->provider_id) {
            abort(403);
        }

        $project->update([
            'provider_marked_complete' => true,
            'completed_at' => now(),
            'client_notified_at' => now(),
            'last_action_by' => 'provider',
        ]);

        \App\Models\ProjectHistory::create([
            'project_id' => $project->id,
            'user_id' => Auth::id(),
            'action' => 'complete_requested',
            'description' => 'Provider marked the project as complete.',
        ]);

        return redirect()->back()->with('success', 'Project marked as complete. Awaiting client approval.');
    }

    public function approve($id)
    {
        $project = Project::findOrFail($id);
        
        if (Auth::id() !== $project->client_id) {
            abort(403);
        }

        $status = 'completed';
        $action = 'approved';
        $description = 'Client approved the project. Funds released.';

        if ($project->termination_requested) {
            $status = 'terminated';
            $action = 'termination_approved';
            $description = 'Client approved the termination request.';
        }

        $project->update([
            'status' => $status,
            'client_approved' => true,
            'escrow_released_at' => $status === 'completed' ? now() : null,
            'last_action_by' => 'client',
        ]);

        \App\Models\ProjectHistory::create([
            'project_id' => $project->id,
            'user_id' => Auth::id(),
            'action' => $action,
            'description' => $description,
        ]);

        return redirect()->back()->with('success', 'Request approved.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string']);
        $project = Project::findOrFail($id);
        
        if (Auth::id() !== $project->client_id && Auth::id() !== $project->provider_id) {
            abort(403);
        }

        $role = Auth::user()->role;
        $action = 'rejected';
        $description = 'Client rejected the request with reason: ' . $request->reason;

        if ($project->provider_marked_complete && $role === 'client') {
            $project->update([
                'provider_marked_complete' => false,
                'rejection_reason' => $request->reason,
                'rejected_at' => now(),
                'last_action_by' => 'client',
            ]);
            $action = 'completion_rejected';
        } elseif ($project->mutual_cancellation_requested && (($project->cancellation_requested_by === 'provider' && $role === 'client') || ($project->cancellation_requested_by === 'client' && $role === 'provider'))) {
            $project->update([
                'status' => 'active', // Revert to active
                'mutual_cancellation_requested' => false,
                'cancellation_requested_by' => null,
                'rejection_reason' => $request->reason,
                'rejected_at' => now(),
                'last_action_by' => $role,
            ]);
            $action = 'cancellation_rejected';
            $description = ucfirst($role) . ' rejected the cancellation request with reason: ' . $request->reason;
        } elseif ($project->termination_requested && $role === 'client') {
            $project->update([
                'termination_requested' => false,
                'rejection_reason' => $request->reason,
                'rejected_at' => now(),
                'last_action_by' => 'client',
            ]);
            $action = 'termination_rejected';
        }

        \App\Models\ProjectHistory::create([
            'project_id' => $project->id,
            'user_id' => Auth::id(),
            'action' => $action,
            'description' => $description,
            'metadata' => ['reason' => $request->reason]
        ]);

        return redirect()->back()->with('success', 'Request rejected.');
    }

    public function requestCancellation($id)
    {
        $project = Project::findOrFail($id);
        
        if (Auth::id() !== $project->client_id && Auth::id() !== $project->provider_id) {
            abort(403);
        }

        $role = Auth::id() === $project->client_id ? 'client' : 'provider';
        $project->update([
            'status' => 'cancelled', // Immediate reflection as requested
            'mutual_cancellation_requested' => true,
            'cancellation_requested_by' => $role,
            'last_action_by' => $role,
        ]);

        \App\Models\ProjectHistory::create([
            'project_id' => $project->id,
            'user_id' => Auth::id(),
            'action' => 'cancellation_requested',
            'description' => ucfirst($role) . ' requested mutual cancellation. Status reflected as cancelled pending confirmation.',
        ]);

        return redirect()->back()->with('success', 'Cancellation requested and reflected.');
    }

    public function confirmCancellation($id)
    {
        $project = Project::findOrFail($id);
        
        if (Auth::id() !== $project->client_id && Auth::id() !== $project->provider_id) {
            abort(403);
        }

        $requestedBy = $project->cancellation_requested_by;
        if (($requestedBy === 'client' && Auth::id() === $project->client_id) || 
            ($requestedBy === 'provider' && Auth::id() === $project->provider_id)) {
            return redirect()->back()->with('error', 'You cannot confirm your own cancellation request.');
        }

        $role = Auth::user()->role;
        $project->update([
            'status' => 'cancelled',
            'last_action_by' => $role,
        ]);

        \App\Models\ProjectHistory::create([
            'project_id' => $project->id,
            'user_id' => Auth::id(),
            'action' => 'cancellation_confirmed',
            'description' => 'Mutual cancellation confirmed by ' . $role . '.',
        ]);

        return redirect()->back()->with('success', 'Project cancelled mutually.');
    }

    public function dispute(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string']);
        $project = Project::findOrFail($id);
        
        if (Auth::id() !== $project->client_id) {
            abort(403);
        }

        $project->update([
            'status' => 'disputed',
            'dispute_reason' => $request->reason,
            'last_action_by' => 'client',
        ]);

        \App\Models\ProjectHistory::create([
            'project_id' => $project->id,
            'user_id' => Auth::id(),
            'action' => 'dispute_raised',
            'description' => 'Client raised a dispute: ' . $request->reason,
            'metadata' => ['reason' => $request->reason]
        ]);

        return redirect()->back()->with('success', 'Project marked as disputed. Admin will review.');
    }

    public function terminate(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string']);
        $project = Project::findOrFail($id);
        
        if (Auth::id() !== $project->provider_id) {
            abort(403);
        }

        $project->update([
            'termination_requested' => true,
            'termination_reason' => $request->reason,
            'termination_requested_at' => now(),
            'last_action_by' => 'provider',
        ]);

        \App\Models\ProjectHistory::create([
            'project_id' => $project->id,
            'user_id' => Auth::id(),
            'action' => 'termination_requested',
            'description' => 'Provider requested termination: ' . $request->reason,
            'metadata' => ['reason' => $request->reason]
        ]);

        return redirect()->back()->with('success', 'Termination requested. Awaiting client approval.');
    }

    public function history($id)
    {
        $project = Project::with(['histories.user'])->findOrFail($id);
        
        if (Auth::user()->role !== 'admin' && Auth::id() !== $project->client_id && Auth::id() !== $project->provider_id) {
            abort(403);
        }

        return response()->json($project->histories);
    }
}
