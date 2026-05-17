<?php

namespace Modules\ClientAPI\GraphQL\Mutations;

use App\Models\Project;
use App\Models\ProjectHistory;
use App\Models\ProviderService;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Payments\Services\TapPaymentService;

class ProjectMutation
{
    /**
     * Handle service request from mobile app.
     */
    public function requestService($_, array $args)
    {
        $user = Auth::user();
        $input = $args['input'];

        $ps = ProviderService::findOrFail($input['provider_service_id']);
        $billingCycle = $input['billing_cycle'];

        // 1. Double Subscription Check (Project + Company level)
        $existingProject = Project::where('client_id', $user->id)
            ->where('company_id', $input['company_id'])
            ->where('provider_id', $ps->provider_id)
            ->where('service_id', $ps->service_id)
            ->whereHas('transactions', function($q) {
                $q->whereIn('status', ['authorized', 'captured', 'CAPTURED', 'AUTHORIZED']);
            })
            ->first();

        if ($existingProject) {
            throw new \Exception('An active project for this service already exists for this company.');
        }

        // 2. Plan Limits Check
        if ($user->plan && $user->projects()->where('status', 'active')->count() >= $user->plan->max_projects) {
            throw new \Exception('Plan project limit reached. Please upgrade your plan.');
        }

        $amount = ($billingCycle === 'annually') ? $ps->annual_price : $ps->monthly_price;

        return DB::transaction(function () use ($user, $ps, $input, $amount, $billingCycle) {
            $project = Project::create([
                'client_id' => $user->id,
                'company_id' => $input['company_id'],
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
                'description' => 'Project initiated via Mobile API, awaiting payment.',
            ]);

            return $project;
        });
    }

    /**
     * Send message within a project workspace.
     */
    public function sendMessage($_, array $args)
    {
        $user = Auth::user();
        $projectId = $args['project_id'];
        $messageText = $args['message'];

        $project = Project::findOrFail($projectId);

        // Security: Ensure the user belongs to the project
        if ($user->id !== $project->client_id && $user->role !== 'admin') {
             throw new \Exception('Unauthorized: You are not a participant in this project.');
        }

        return Message::create([
            'project_id' => $project->id,
            'user_id' => $user->id,
            'message' => $messageText,
        ]);
    }
}
