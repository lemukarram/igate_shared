<?php

namespace Modules\Admin\Filament\Widgets;

use App\Models\Project;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestProjects extends BaseWidget
{
    protected static ?int $sort = 10;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Project::query()->latest()->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('company.name')
                    ->label('Client'),
                Tables\Columns\TextColumn::make('provider.providerProfile.company_name')
                    ->label('Provider'),
                Tables\Columns\TextColumn::make('service.name')
                    ->label('Service'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'info',
                        'completed' => 'success',
                        'disputed' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('total_amount')
                    ->money('SAR'),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->url(fn (Project $record): string => "/admin/projects/{$record->id}/edit")
                    ->icon('heroicon-m-eye'),
            ]);
    }
}
