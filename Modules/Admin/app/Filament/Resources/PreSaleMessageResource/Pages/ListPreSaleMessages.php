<?php

namespace Modules\Admin\Filament\Resources\PreSaleMessageResource\Pages;

use Modules\Admin\Filament\Resources\PreSaleMessageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPreSaleMessages extends ListRecords
{
    protected static string $resource = PreSaleMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
