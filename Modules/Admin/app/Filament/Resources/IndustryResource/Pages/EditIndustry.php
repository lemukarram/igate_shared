<?php

namespace Modules\Admin\Filament\Resources\IndustryResource\Pages;

use Modules\Admin\Filament\Resources\IndustryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditIndustry extends EditRecord
{
    protected static string $resource = IndustryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
