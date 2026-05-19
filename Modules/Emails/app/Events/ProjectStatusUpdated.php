<?php

namespace Modules\Emails\Events;

use App\Models\Project;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProjectStatusUpdated
{
    use Dispatchable, SerializesModels;

    public Project $project;
    public string $newStatus;

    public function __construct(Project $project, string $newStatus)
    {
        $this->project = $project;
        $this->newStatus = $newStatus;
    }
}
