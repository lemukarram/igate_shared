<?php

namespace Modules\Admin\Filament\Resources\InternalMessageResource\Pages;

use Modules\Admin\Filament\Resources\InternalMessageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInternalMessages extends ListRecords
{
    protected static string $resource = InternalMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
