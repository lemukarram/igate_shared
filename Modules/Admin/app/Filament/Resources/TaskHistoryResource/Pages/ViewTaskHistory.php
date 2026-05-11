<?php

namespace Modules\Admin\Filament\Resources\TaskHistoryResource\Pages;

use Modules\Admin\Filament\Resources\TaskHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTaskHistory extends ViewRecord
{
    protected static string $resource = TaskHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
