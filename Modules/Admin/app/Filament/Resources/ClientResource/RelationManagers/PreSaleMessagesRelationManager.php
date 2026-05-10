<?php

namespace Modules\Admin\Filament\Resources\ClientResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PreSaleMessagesRelationManager extends RelationManager
{
    protected static string $relationship = 'preSaleMessages';

    protected static ?string $title = 'Sales / Pre Sale Chats';

    protected static ?string $icon = 'heroicon-o-chat-bubble-left-right';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('message')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('message')
            ->columns([
                Tables\Columns\TextColumn::make('provider.name')
                    ->label('Provider'),
                Tables\Columns\TextColumn::make('service.name')
                    ->label('Service'),
                Tables\Columns\TextColumn::make('message')
                    ->limit(50),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn ($record) => "/admin/pre-sale-messages/{$record->id}"),
            ])
            ->bulkActions([
                //
            ]);
    }
}
