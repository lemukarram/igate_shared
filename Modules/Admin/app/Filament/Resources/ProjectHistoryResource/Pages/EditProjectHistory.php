<?php

namespace Modules\Admin\Filament\Resources\ProjectHistoryResource\Pages;

use Modules\Admin\Filament\Resources\ProjectHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProjectHistory extends EditRecord
{
    protected static string $resource = ProjectHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
