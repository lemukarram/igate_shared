<?php

namespace Modules\Admin\Filament\Resources\IndustryResource\Pages;

use Modules\Admin\Filament\Resources\IndustryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListIndustries extends ListRecords
{
    protected static string $resource = IndustryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
