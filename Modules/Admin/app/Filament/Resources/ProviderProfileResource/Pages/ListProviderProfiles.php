<?php

namespace Modules\Admin\Filament\Resources\ProviderProfileResource\Pages;

use Modules\Admin\Filament\Resources\ProviderProfileResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProviderProfiles extends ListRecords
{
    protected static string $resource = ProviderProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
