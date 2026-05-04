<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ProjectAutoManagement extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'projects:auto-manage';
    protected $description = 'Handle auto-approval of completed projects and detect SLA breaches.';

    public function handle()
    {
        $this->handleAutoApproval();
        $this->handleSLABreaches();
    }

    private function handleAutoApproval()
    {
        // Scenario 2: System Auto-Approval (7 days)
        $projectsToApprove = \App\Models\Project::where('provider_marked_complete', true)
            ->where('status', '!=', 'completed')
            ->where('client_notified_at', '<=', now()->subDays(7))
            ->get();

        foreach ($projectsToApprove as $project) {
            $project->update([
                'status' => 'completed',
                'client_approved' => true, // System approved on behalf of client
                'escrow_released_at' => now(),
            ]);
            $this->info("Project #{$project->id} auto-approved.");
        }
    }

    private function handleSLABreaches()
    {
        // Scenario 6: SLA Auto-Breach
        // We look for active projects where current time > start_date + delivery_time_days
        $activeProjects = \App\Models\Project::where('status', 'active')
            ->with(['service.providerServices' => function($query) {
                // This might need refinement to get the specific provider's service
            }])
            ->get();

        foreach ($activeProjects as $project) {
            // Get provider service to find delivery_time_days
            $providerService = \App\Models\ProviderService::where('provider_id', $project->provider_id)
                ->where('service_id', $project->service_id)
                ->first();

            if ($providerService) {
                $dueDate = $project->start_date ? \Carbon\Carbon::parse($project->start_date)->addDays($providerService->delivery_time_days) : null;
                
                if ($dueDate && now()->gt($dueDate)) {
                    $project->update(['status' => 'sla_breached']);
                    $this->warn("Project #{$project->id} marked as SLA Breached.");
                }
            }
        }
    }
}
