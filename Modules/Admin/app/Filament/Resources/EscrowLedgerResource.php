<?php

namespace Modules\Admin\Filament\Resources;

use Modules\Admin\Filament\Resources\EscrowLedgerResource\Pages;
use App\Models\EscrowLedger;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EscrowLedgerResource extends Resource
{
    protected static ?string $model = EscrowLedger::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-currency-dollar';
    protected static ?string $navigationGroup = 'Financials';
    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Escrow Movement';
    protected static ?string $pluralModelLabel = 'Escrow Ledger';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Read-only, no form needed typically, but provided for view mode
                Forms\Components\TextInput::make('project_id'),
                Forms\Components\TextInput::make('amount'),
                Forms\Components\TextInput::make('type'),
                Forms\Components\Textarea::make('description'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('project.id')
                    ->label('Project ID')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->money()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'credit' => 'success',
                        'debit' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('description')
                    ->limit(50),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'credit' => 'Credit (In)',
                        'debit' => 'Debit (Out)',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // Read-only
            ]);
    }

    public static function canCreate(): bool
    {
        return false; // Read-only
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEscrowLedgers::route('/'),
        ];
    }
}
