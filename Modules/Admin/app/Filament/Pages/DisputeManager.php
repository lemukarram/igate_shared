<?php

namespace Modules\Admin\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Project;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Actions\Action;

class DisputeManager extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-scale';
    protected static ?string $navigationGroup = 'Operations';
    protected static ?int $navigationSort = 4;
    protected static string $view = 'modules.admin.filament.pages.dispute-manager';

    public function table(Table $table): Table
    {
        return $table
            ->query(Project::query()->where('status', 'disputed'))
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('Project ID'),
                Tables\Columns\TextColumn::make('client.name')->label('Client'),
                Tables\Columns\TextColumn::make('provider.name')->label('Provider'),
                Tables\Columns\TextColumn::make('total_amount')->money(),
                Tables\Columns\TextColumn::make('dispute_reason')->limit(30),
            ])
            ->actions([
                Action::make('split_funds')
                    ->label('Split Funds')
                    ->color('warning')
                    ->icon('heroicon-o-arrows-right-left')
                    ->requiresConfirmation()
                    ->action(fn (Project $record) => $record->update(['status' => 'completed'])),
                Action::make('refund_client')
                    ->label('Refund Client')
                    ->color('danger')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->requiresConfirmation()
                    ->action(fn (Project $record) => $record->update(['status' => 'cancelled'])),
                Action::make('release_provider')
                    ->label('Release to Provider')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->action(fn (Project $record) => $record->update(['status' => 'completed'])),
            ]);
    }
}
