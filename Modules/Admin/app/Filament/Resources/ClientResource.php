<?php

namespace Modules\Admin\Filament\Resources;

use Modules\Admin\Filament\Resources\ClientResource\Pages;
use Modules\Admin\Filament\Resources\ClientResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class ClientResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $slug = 'clients';

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Clients';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'Client';
    protected static ?string $pluralModelLabel = 'Clients List';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('role', 'client');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Client Account Details')->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),
                    Forms\Components\TextInput::make('phone')
                        ->tel()
                        ->maxLength(255),
                    Forms\Components\Select::make('plan_id')
                        ->label('Subscription Plan')
                        ->relationship('plan', 'name', fn ($query) => $query->where('type', 'client'))
                        ->searchable()
                        ->preload(),
                    Forms\Components\TextInput::make('password')
                        ->password()
                        ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                        ->dehydrated(fn ($state) => filled($state))
                        ->required(fn (string $context): bool => $context === 'create'),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('profile_picture')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('plan.name')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('companies_count')
                    ->label('Companies')
                    ->counts('companies'),
                Tables\Columns\TextColumn::make('projects_count')
                    ->label('Projects')
                    ->counts('projects'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('plan_id')
                    ->label('Plan')
                    ->relationship('plan', 'name', fn ($query) => $query->where('type', 'client')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
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
            RelationManagers\CompaniesRelationManager::class,
            RelationManagers\ProjectsRelationManager::class,
            RelationManagers\PreSaleMessagesRelationManager::class,
            RelationManagers\InternalMessagesRelationManager::class,
            RelationManagers\StaffRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
            'view' => Pages\ViewClient::route('/{record}'),
        ];
    }
}
