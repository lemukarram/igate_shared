<?php

namespace Modules\Admin\Filament\Pages;

use Filament\Pages\Page;

class ApiDocumentation extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Systems';
    protected static ?int $navigationSort = 10;
    protected static ?string $title = 'API Documentation';
    protected static string $view = 'modules.admin.filament.pages.api-documentation';
}
