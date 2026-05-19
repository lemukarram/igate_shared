<?php

namespace Modules\Admin\Filament\Resources\EmailTemplateResource\Pages;

use Modules\Admin\Filament\Resources\EmailTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmailTemplate extends EditRecord
{
    protected static string $resource = EmailTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
