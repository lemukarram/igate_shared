<?php

namespace Modules\Admin\Filament\Resources\EscrowLedgerResource\Pages;

use Modules\Admin\Filament\Resources\EscrowLedgerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEscrowLedgers extends ListRecords
{
    protected static string $resource = EscrowLedgerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
