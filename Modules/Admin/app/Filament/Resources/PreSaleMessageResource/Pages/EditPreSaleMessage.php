<?php

namespace Modules\Admin\Filament\Resources\PreSaleMessageResource\Pages;

use Modules\Admin\Filament\Resources\PreSaleMessageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPreSaleMessage extends EditRecord
{
    protected static string $resource = PreSaleMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
