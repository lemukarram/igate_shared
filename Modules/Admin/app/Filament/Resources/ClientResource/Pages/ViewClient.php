<?php

namespace Modules\Admin\Filament\Resources\ClientResource\Pages;

use Modules\Admin\Filament\Resources\ClientResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewClient extends ViewRecord
{
    protected static string $resource = ClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
