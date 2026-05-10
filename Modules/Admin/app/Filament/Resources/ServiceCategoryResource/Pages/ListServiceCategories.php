<?php

namespace Modules\Admin\Filament\Resources\ServiceCategoryResource\Pages;

use Modules\Admin\Filament\Resources\ServiceCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListServiceCategories extends ListRecords
{
    protected static string $resource = ServiceCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
