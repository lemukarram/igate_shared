<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Team;
use Illuminate\Support\Facades\Auth;

class TeamTaskController extends Controller
{
    protected function getProviderId()
    {
        $user = Auth::user();
        if ($user->role === 'provider') {
            return $user->id;
        }
        
        // If team member, find owner via their team
        $membership = \App\Models\TeamMember::where('user_id', $user->id)->first();
        if ($membership && $membership->team) {
            return $membership->team->owner_id;
        }
        
        return $user->id;
    }

    public function index()
    {
        $providerId = $this->getProviderId();
        $query = Task::where('provider_id', $providerId)
                     ->whereNull('project_id')
                     ->with('assignedUser');

        $tasks = $query->get()->groupBy('status');
        
        $team = Team::where('owner_id', $providerId)->first();
        $teamMembers = $team ? $team->members()->with('user')->get() : collect();

        return view('provider.team_tasks', compact('tasks', 'teamMembers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'priority' => 'required|in:normal,high,urgent',
            'status' => 'required|in:todo,in_progress,review,done',
            'files.*' => 'nullable|file|max:10240',
        ]);

        if (empty($validated['assigned_to'])) {
            $validated['assigned_to'] = null;
        }

        $providerId = $this->getProviderId();
        $validated['provider_id'] = $providerId;
        
        $team = Team::where('owner_id', $providerId)->first();
        if ($team) {
            $validated['team_id'] = $team->id;
        }

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
                    'project_id' => $task->project_id ?? null,
                    'name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        return redirect()->back()->with('success', __('common.task_created_success'));
    }

    public function show($id)
    {
        $providerId = $this->getProviderId();
        $task = Task::where('provider_id', $providerId)
                    ->with(['assignedUser', 'documents', 'histories.user'])
                    ->findOrFail($id);
        
        // Format history for frontend display
        $task->histories->each(function($h) {
            $h->field_label = $h->field ? __('common.' . $h->field) : null;
            $h->old_value_label = $this->resolveHistoryValue($h->field, $h->old_value);
            $h->new_value_label = $this->resolveHistoryValue($h->field, $h->new_value);
        });

        return response()->json($task);
    }

    public function update(Request $request, $id)
    {
        $providerId = $this->getProviderId();
        $task = Task::where('provider_id', $providerId)->findOrFail($id);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'priority' => 'required|in:normal,high,urgent',
            'status' => 'required|in:todo,in_progress,review,done',
            'files.*' => 'nullable|file|max:10240',
        ]);

        if (empty($validated['assigned_to'])) {
            $validated['assigned_to'] = null;
        }

        $oldValues = $task->only(['title', 'description', 'assigned_to', 'due_date', 'priority', 'status']);
        
        $task->update($validated);

        // Record history for changed fields
        foreach ($validated as $key => $newValue) {
            if ($key === 'files') continue;
            
            $oldValue = $oldValues[$key] ?? null;
            
            // Normalize for comparison
            $compOld = $oldValue;
            $compNew = $newValue;
            
            if ($key === 'due_date') {
                $compOld = $oldValue ? \Carbon\Carbon::parse($oldValue)->format('Y-m-d') : null;
                $compNew = $newValue ? \Carbon\Carbon::parse($newValue)->format('Y-m-d') : null;
            }

            if ($compOld != $compNew) {
                \App\Models\TaskHistory::create([
                    'task_id' => $task->id,
                    'user_id' => Auth::id(),
                    'field' => $key,
                    'old_value' => $oldValue,
                    'new_value' => $newValue,
                    'action' => 'updated',
                ]);
            }
        }

        // Handle additional file uploads
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('documents', 'public');
                \App\Models\Document::create([
                    'task_id' => $task->id,
                    'user_id' => Auth::id(),
                    'project_id' => $task->project_id ?? null,
                    'name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        return redirect()->back()->with('success', __('common.task_updated_success'));
    }

    public function deleteDocument($id)
    {
        $document = \App\Models\Document::findOrFail($id);
        
        // Security check: must be owner of task or uploader
        $task = Task::find($document->task_id);
        if ($document->user_id !== Auth::id() && ($task && $task->provider_id !== Auth::id())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        \Illuminate\Support\Facades\Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return response()->json(['success' => true]);
    }

    protected function resolveHistoryValue($field, $value)
    {
        if (is_null($value) || $value === "") return __('common.none');

        switch ($field) {
            case 'assigned_to':
                return \App\Models\User::find($value)->name ?? $value;
            case 'status':
            case 'priority':
                return __('tasks.' . $value);
            case 'due_date':
                try {
                    return \Carbon\Carbon::parse($value)->format('Y-m-d');
                } catch (\Exception $e) {
                    return $value;
                }
            default:
                return $value;
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:todo,in_progress,review,done',
        ]);

        $providerId = $this->getProviderId();
        $task = Task::where('provider_id', $providerId)->findOrFail($id);
        $oldStatus = $task->status;
        $task->update(['status' => $validated['status']]);

        \App\Models\TaskHistory::create([
            'task_id' => $task->id,
            'user_id' => Auth::id(),
            'field' => 'status',
            'old_value' => $oldStatus,
            'new_value' => $validated['status'],
            'action' => 'status_changed',
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'task' => $task]);
        }
        
        return redirect()->back()->with('success', 'Task status updated.');
    }
}
