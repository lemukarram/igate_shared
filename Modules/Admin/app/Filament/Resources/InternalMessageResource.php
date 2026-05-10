<?php

namespace Modules\Admin\Filament\Resources;

use App\Models\InternalMessage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\Admin\Filament\Resources\InternalMessageResource\Pages;
use Illuminate\Database\Eloquent\Builder;

class InternalMessageResource extends Resource
{
    protected static ?string $model = InternalMessage::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';
    protected static ?string $navigationGroup = 'Clients';
    protected static ?int $navigationSort = 4;
    protected static ?string $modelLabel = 'Internal Message';
    protected static ?string $pluralModelLabel = 'Internal Messages';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Message Details')
                    ->schema([
                        Forms\Components\Select::make('team_id')
                            ->relationship('team', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Textarea::make('message')
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('team.name')
                    ->label('Team / Organization')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Sender')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('message')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('team_id')
                    ->relationship('team', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListInternalMessages::route('/'),
            'create' => Pages\CreateInternalMessage::route('/create'),
            'edit' => Pages\EditInternalMessage::route('/{record}/edit'),
        ];
    }
}
