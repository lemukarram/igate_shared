<?php

namespace Modules\Admin\Filament\Widgets;

use Filament\Widgets\Widget;

class QuickActions extends Widget
{
    protected static ?int $sort = 2;

    protected static string $view = 'admin::filament.widgets.quick-actions';

    protected int | string | array $columnSpan = 'full';
}
