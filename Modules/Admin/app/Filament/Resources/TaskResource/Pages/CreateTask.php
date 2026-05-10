<?php

namespace Modules\Admin\Filament\Resources\TaskResource\Pages;

use Modules\Admin\Filament\Resources\TaskResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTask extends CreateRecord
{
    protected static string $resource = TaskResource::class;
}
