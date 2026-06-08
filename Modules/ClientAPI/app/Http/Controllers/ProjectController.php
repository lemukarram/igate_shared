<?php

namespace Modules\ClientAPI\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectHistory;
use App\Models\ProviderService;
use App\Models\Message;
use App\Models\Document;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\Task;
use App\Models\TaskHistory;
use App\Models\ClientActivity;
use App\Traits\HandlesApiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    use HandlesApiResponses;

    public function index()
    {
        return $this->successResponse(Auth::user()->projects()->with(['service', 'company'])->latest()->get());
    }

    public function show($id)
    {
        try {
            $project = Auth::user()->projects()->with(['service', 'company', 'tasks', 'documents', 'messages.user'])->findOrFail($id);

            // Auto-populate tasks from service subtasks if none exist (Replicating main website logic)
            if ($project->tasks->isEmpty() && $project->service && $project->service->subtasks) {
                foreach ($project->service->subtasks as $subtask) {
                    Task::create([
                        'project_id' => $project->id,
                        'provider_id' => $project->provider_id,
                        'title' => $subtask,
                        'status' => 'todo',
                    ]);
                }
                // Refresh tasks relation
                $project->load('tasks');
            }

            return $this->successResponse($project);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'provider_service_id' => 'required|exists:provider_services,id',
                'company_id' => 'required|exists:companies,id',
                'billing_cycle' => 'required|in:monthly,annual,annually',
            ]);

            $billingCycle = $request->billing_cycle === 'annual' ? 'annually' : $request->billing_cycle;

            $user = Auth::user();
            $ps = ProviderService::findOrFail($request->provider_service_id);

            // Double Subscription Check
            $existing = Project::where('client_id', $user->id)
                ->where('company_id', $request->company_id)
                ->where('service_id', $ps->service_id)
                ->where('status', 'active')
                ->first();

            if ($existing) {
                return $this->errorResponse('Active project already exists for this company', 422);
            }

            $amount = ($billingCycle === 'annually') ? $ps->annual_price : $ps->monthly_price;

            $project = DB::transaction(function () use ($user, $ps, $request, $amount) {
                $project = Project::create([
                    'client_id' => $user->id,
                    'company_id' => $request->company_id,
                    'provider_id' => $ps->provider_id,
                    'service_id' => $ps->service_id,
                    'provider_service_id' => $ps->id,
                    'status' => 'pending_payment',
                    'total_amount' => $amount,
                    'start_date' => now(),
                ]);

                ProjectHistory::create([
                    'project_id' => $project->id,
                    'user_id' => $user->id,
                    'action' => 'project_initiated',
                    'description' => 'Project initiated via API, awaiting payment.',
                ]);

                return $project;
            });

            return $this->successResponse($project, 'Project initiated successfully', 201);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function sendMessage(Request $request, $id)
    {
        try {
            $project = Auth::user()->projects()->findOrFail($id);
            
            $validated = $request->validate(['message' => 'required|string']);

            $message = Message::create([
                'project_id' => $project->id,
                'user_id' => Auth::id(),
                'message' => $validated['message'],
            ]);

            return $this->successResponse($message, 'Message sent successfully', 201);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function uploadDocument(Request $request, $id)
    {
        try {
            $project = Auth::user()->projects()->findOrFail($id);
            
            $request->validate([
                'name' => 'required|string',
                'file' => 'required|file|max:10240', // 10MB
            ]);

            $path = $request->file('file')->store('documents', 'public');

            $document = Document::create([
                'user_id' => Auth::id(),
                'project_id' => $project->id,
                'name' => $request->name,
                'file_path' => $path,
                'file_type' => $request->file('file')->getClientOriginalExtension(),
                'file_size' => $request->file('file')->getSize(),
            ]);

            return $this->successResponse($document, 'Document uploaded successfully', 201);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function documents($id)
    {
        try {
            $project = Auth::user()->projects()->findOrFail($id);
            $documents = Document::where('project_id', $project->id)->with('user')->get();
            return $this->successResponse($documents);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function updateTaskStatus(Request $request, $id, $taskId)
    {
        try {
            $request->validate(['status' => 'required|in:todo,in_progress,review,done']);
            $project = Auth::user()->projects()->findOrFail($id);

            if ($project->status === 'inactive' && Auth::user()->role !== 'admin') {
                return $this->errorResponse('Project is inactive. Actions are restricted.', 403);
            }

            $task = Task::where('project_id', $project->id)->findOrFail($taskId);
            $oldStatus = $task->status;

            $updateData = ['status' => $request->status];
            if ($request->status !== 'done') {
                $updateData['is_verified'] = false;
                $updateData['verified_at'] = null;
            }

            $task->update($updateData);

            TaskHistory::create([
                'task_id' => $task->id,
                'user_id' => Auth::id(),
                'field' => 'status',
                'old_value' => $oldStatus,
                'new_value' => $request->status,
                'action' => 'status_changed',
            ]);

            ClientActivity::create([
                'client_id' => $project->client_id,
                'provider_id' => $project->provider_id,
                'project_id' => $project->id,
                'activity_type' => 'task_status_changed',
                'description' => 'Task "' . $task->title . '" status changed from ' . $oldStatus . ' to ' . $request->status . '.',
            ]);

            return $this->successResponse($task, 'Task status updated.');
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function verifyTask(Request $request, $id, $taskId)
    {
        try {
            $project = Auth::user()->projects()->findOrFail($id);

            if ($project->status === 'inactive' && Auth::user()->role !== 'admin') {
                return $this->errorResponse('Project is inactive. Actions are restricted.', 403);
            }

            $task = Task::where('project_id', $project->id)->findOrFail($taskId);

            if ($task->status !== 'done') {
                return $this->errorResponse('Only completed tasks can be verified.', 422);
            }

            $task->update([
                'is_verified' => true,
                'verified_at' => now(),
            ]);

            TaskHistory::create([
                'task_id' => $task->id,
                'user_id' => Auth::id(),
                'action' => 'verified',
            ]);

            return $this->successResponse($task, 'Task verified successfully.');
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function approve($id)
    {
        try {
            $project = Auth::user()->projects()->findOrFail($id);

            if ($project->status === 'inactive' && Auth::user()->role !== 'admin') {
                return $this->errorResponse('Project is inactive. Actions are restricted.', 403);
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

            ProjectHistory::create([
                'project_id' => $project->id,
                'user_id' => Auth::id(),
                'action' => $action,
                'description' => $description,
            ]);

            return $this->successResponse($project, 'Request approved.');
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function reject(Request $request, $id)
    {
        try {
            $request->validate(['reason' => 'required|string']);
            $project = Auth::user()->projects()->findOrFail($id);

            if ($project->status === 'inactive' && Auth::user()->role !== 'admin') {
                return $this->errorResponse('Project is inactive. Actions are restricted.', 403);
            }

            $action = 'rejected';
            $description = 'Client rejected the request with reason: ' . $request->reason;

            if ($project->provider_marked_complete) {
                $project->update([
                    'provider_marked_complete' => false,
                    'rejection_reason' => $request->reason,
                    'rejected_at' => now(),
                    'last_action_by' => 'client',
                ]);
                $action = 'completion_rejected';
            } elseif ($project->mutual_cancellation_requested && $project->cancellation_requested_by === 'provider') {
                $project->update([
                    'status' => 'active',
                    'mutual_cancellation_requested' => false,
                    'cancellation_requested_by' => null,
                    'rejection_reason' => $request->reason,
                    'rejected_at' => now(),
                    'last_action_by' => 'client',
                ]);
                $action = 'cancellation_rejected';
                $description = 'Client rejected the cancellation request with reason: ' . $request->reason;
            } elseif ($project->termination_requested) {
                $project->update([
                    'termination_requested' => false,
                    'rejection_reason' => $request->reason,
                    'rejected_at' => now(),
                    'last_action_by' => 'client',
                ]);
                $action = 'termination_rejected';
            }

            ProjectHistory::create([
                'project_id' => $project->id,
                'user_id' => Auth::id(),
                'action' => $action,
                'description' => $description,
                'metadata' => ['reason' => $request->reason]
            ]);

            return $this->successResponse($project, 'Request rejected.');
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function requestCancellation($id)
    {
        try {
            $project = Auth::user()->projects()->findOrFail($id);

            if ($project->status === 'inactive' && Auth::user()->role !== 'admin') {
                return $this->errorResponse('Project is inactive. Actions are restricted.', 403);
            }

            $project->update([
                'status' => 'cancelled',
                'mutual_cancellation_requested' => true,
                'cancellation_requested_by' => 'client',
                'last_action_by' => 'client',
            ]);

            ProjectHistory::create([
                'project_id' => $project->id,
                'user_id' => Auth::id(),
                'action' => 'cancellation_requested',
                'description' => 'Client requested mutual cancellation. Status reflected as cancelled pending confirmation.',
            ]);

            return $this->successResponse($project, 'Cancellation requested and reflected.');
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function subscriptions()
    {
        return $this->successResponse(Auth::user()->subscriptions()->with(['service', 'company'])->latest()->get());
    }

    public function transactions()
    {
        return $this->successResponse(Transaction::where('user_id', Auth::id())->latest()->get());
    }
}
