<?php

namespace Modules\Admin\Filament\Resources\ProviderServiceResource\Pages;

use Modules\Admin\Filament\Resources\ProviderServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProviderServices extends ListRecords
{
    protected static string $resource = ProviderServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
