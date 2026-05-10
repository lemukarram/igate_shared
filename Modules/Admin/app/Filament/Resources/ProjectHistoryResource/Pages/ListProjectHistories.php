<?php

namespace Modules\Admin\Filament\Resources\ProjectHistoryResource\Pages;

use Modules\Admin\Filament\Resources\ProjectHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProjectHistories extends ListRecords
{
    protected static string $resource = ProjectHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
