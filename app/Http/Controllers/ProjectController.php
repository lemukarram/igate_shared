<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Modules\Payments\Services\TapPaymentService;
use App\Traits\SyncsProjectPayment;

class ProjectController extends Controller
{
    use SyncsProjectPayment;

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

        // Payment Status Check for Clients and Providers
        if (Auth::user()->role !== 'admin') {
            $transaction = $this->syncProjectPayment($project);
            $project->refresh(); // Ensure the instance has the latest status after sync
            
            if ($transaction) {
                $authorizedStatuses = ['authorized', 'captured'];
                
                if (!in_array(strtolower($transaction->status), $authorizedStatuses)) {
                    return redirect()->route('projects.payment-review', $project->id);
                }
            }
        }

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

        $userReview = \App\Models\Review::where('project_id', $project->id)
            ->where('reviewer_id', Auth::id())
            ->first();

        // 1. Try to find an explicit subscription record
        $subscription = \App\Models\Subscription::where('client_id', $project->client_id)
            ->where('service_id', $project->service_id)
            ->where(function($q) use ($project) {
                $q->where('company_id', $project->company_id)
                  ->orWhereNull('company_id');
            })
            ->latest()
            ->first();

        // 2. Fallback: Check the transaction for this project
        if (!$subscription || !$subscription->ends_at) {
            $transaction = \App\Models\Transaction::where('project_id', $project->id)
                ->whereIn('status', ['authorized', 'captured', 'CAPTURED', 'AUTHORIZED'])
                ->latest()
                ->first();
            
            if ($transaction) {
                $billingCycle = $transaction->billing_cycle ?? 'monthly';
                $endsAt = ($billingCycle === 'annually') 
                    ? $transaction->created_at->addYear() 
                    : $transaction->created_at->addMonth();
                
                $subscription = new \App\Models\Subscription([
                    'ends_at' => $endsAt,
                    'status' => 'active',
                ]);
            }
        }

        // 3. Last Resort Fallback: If project is active, use created_at
        if (!$subscription || !$subscription->ends_at) {
            if ($project->status === 'active') {
                $subscription = new \App\Models\Subscription([
                    'ends_at' => $project->created_at->addMonth(),
                    'status' => 'active',
                ]);
            }
        }

        return view('projects.show', compact('project', 'messages', 'userReview', 'subscription'));
    }

    public function updateStatus(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        
        if (Auth::user()->role !== 'admin' && Auth::id() !== $project->provider_id) {
            abort(403);
        }

        if ($project->status === 'inactive' && Auth::user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Project is inactive. Actions are restricted.');
        }

        $request->validate([
            'status' => 'required|in:active,inactive,pending',
        ]);

        $oldStatus = $project->status;
        $project->update(['status' => $request->status]);

        // Dispatch Project Status Updated Event
        event(new \Modules\Emails\Events\ProjectStatusUpdated($project, $request->status));

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

        if ($project->status === 'inactive' && Auth::user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Project is inactive. Actions are restricted.');
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

        if ($project->status === 'inactive' && Auth::user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Project is inactive. Actions are restricted.');
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

        if ($project->status === 'inactive' && Auth::user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Project is inactive. Actions are restricted.');
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

        if ($project->status === 'inactive' && Auth::user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Project is inactive. Actions are restricted.');
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

        if ($project->status === 'inactive' && Auth::user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Project is inactive. Actions are restricted.');
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

        if ($project->status === 'inactive' && Auth::user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Project is inactive. Actions are restricted.');
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

        if ($project->status === 'inactive' && Auth::user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Project is inactive. Actions are restricted.');
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
        $query = Project::with(['histories.user', 'tasks.histories.user']);

        if (Auth::user()->role !== 'admin') {
            // Include logic to handle team members if they access via provider/client IDs,
            // this matches the show() method's basic checks or allows access if the user can view the project.
            $query->where(function($q) {
                $q->where('client_id', Auth::id())
                  ->orWhere('provider_id', Auth::id());
            });
        }

        $project = $query->findOrFail($id);

        $projectHistories = $project->histories->map(function ($h) {
            return [
                'id' => 'p_' . $h->id,
                'description' => $h->description,
                'action' => $h->action,
                'created_at' => $h->created_at,
                'user' => $h->user
            ];
        });

        $taskHistories = $project->tasks->flatMap(function ($task) {
            return $task->histories->map(function ($h) use ($task) {
                $desc = '';
                if ($h->action === 'status_changed') {
                    $desc = "Status changed from {$h->old_value} to {$h->new_value}";
                } elseif ($h->action === 'created') {
                    $desc = "Task created";
                } elseif ($h->action === 'verified') {
                    $desc = "Task verified";
                } else {
                    $desc = "Action: {$h->action}";
                }

                return [
                    'id' => 't_' . $h->id,
                    'description' => 'Task [' . $task->title . ']: ' . $desc,
                    'action' => $h->action,
                    'created_at' => $h->created_at,
                    'user' => $h->user
                ];
            });
        });

        $allHistories = collect($projectHistories)
            ->concat($taskHistories)
            ->sortByDesc('created_at')
            ->values();

        return response()->json($allHistories);
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

    public function paymentReview($id)
    {
        $project = Project::findOrFail($id);
        
        if (Auth::id() !== $project->client_id && Auth::id() !== $project->provider_id && Auth::user()->role !== 'admin') {
            abort(403);
        }

        $transaction = \App\Models\Transaction::where('project_id', $project->id)->first();

        return view('projects.payment_review', compact('project', 'transaction'));
    }

    public function contactSupport(Request $request)
    {
        $projectId = $request->query('project_id');
        $transactionId = $request->query('transaction_id');
        
        $project = $projectId ? Project::find($projectId) : null;
        
        $sampleMessage = "Hello Support,\n\nI am having an issue with my payment for project " . ($project ? $project->id : 'N/A') . ". The transaction ID is " . ($transactionId ?? 'N/A') . ". The status is currently showing as pending/under review. Please assist.\n\nThank you.";

        return view('support.contact', compact('project', 'transactionId', 'sampleMessage'));
    }

    public function submitSupport(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'project_id' => 'nullable|exists:projects,id',
            'transaction_id' => 'nullable|string',
        ]);

        \App\Models\SupportTicket::create([
            'user_id' => Auth::id(),
            'project_id' => $request->project_id,
            'transaction_id' => $request->transaction_id,
            'message' => $request->message,
            'status' => 'open',
        ]);

        return redirect()->route(Auth::user()->active_portal . '.dashboard')->with('success', 'Support ticket submitted successfully. We will get back to you soon.');
    }
}
