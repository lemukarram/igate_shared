<?php

namespace Modules\Admin\Filament\Resources\EmailTemplateResource\Pages;

use Modules\Admin\Filament\Resources\EmailTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmailTemplates extends ListRecords
{
    protected static string $resource = EmailTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No Create action as templates are seeded
        ];
    }
}
