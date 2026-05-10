<?php

namespace Modules\Admin\Filament\Resources\ClientUserResource\Pages;

use Modules\Admin\Filament\Resources\ClientUserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateClientUser extends CreateRecord
{
    protected static string $resource = ClientUserResource::class;
}
