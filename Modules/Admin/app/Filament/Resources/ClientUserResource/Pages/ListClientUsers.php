<?php

namespace Modules\Admin\Filament\Resources\ClientUserResource\Pages;

use Modules\Admin\Filament\Resources\ClientUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClientUsers extends ListRecords
{
    protected static string $resource = ClientUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
