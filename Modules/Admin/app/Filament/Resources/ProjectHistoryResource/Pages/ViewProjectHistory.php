<?php

namespace Modules\Admin\Filament\Resources\ProjectHistoryResource\Pages;

use Modules\Admin\Filament\Resources\ProjectHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewProjectHistory extends ViewRecord
{
    protected static string $resource = ProjectHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
