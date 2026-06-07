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
                'billing_cycle' => 'required|in:monthly,annually',
            ]);

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

            $amount = ($request->billing_cycle === 'annually') ? $ps->annual_price : $ps->monthly_price;

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

    public function subscriptions()
    {
        return $this->successResponse(Auth::user()->subscriptions()->with(['service', 'company'])->latest()->get());
    }

    public function transactions()
    {
        return $this->successResponse(Transaction::where('user_id', Auth::id())->latest()->get());
    }
}
