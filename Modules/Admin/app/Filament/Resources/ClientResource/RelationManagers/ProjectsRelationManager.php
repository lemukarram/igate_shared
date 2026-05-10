<?php

namespace Modules\Admin\Filament\Resources\ClientResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ProjectsRelationManager extends RelationManager
{
    protected static string $relationship = 'projects';

    protected static ?string $title = 'Purchased Projects';

    protected static ?string $icon = 'heroicon-o-briefcase';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('status')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('status')
            ->columns([
                Tables\Columns\TextColumn::make('company.name')
                    ->label('Company'),
                Tables\Columns\TextColumn::make('service.name')
                    ->label('Service'),
                Tables\Columns\TextColumn::make('provider.name')
                    ->label('Provider'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'active' => 'info',
                        'completed' => 'success',
                        'disputed' => 'danger',
                        'cancelled' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('total_amount')
                    ->money('SAR'),
                Tables\Columns\TextColumn::make('end_date')
                    ->dateTime('M d, Y'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn ($record) => "/admin/projects/{$record->id}/edit"),
            ])
            ->bulkActions([
                //
            ]);
    }
}
