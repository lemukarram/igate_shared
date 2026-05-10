<?php

namespace Modules\Admin\Filament\Resources;

use Modules\Admin\Filament\Resources\ClientUserResource\Pages;
use Modules\Admin\Filament\Resources\ClientUserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ClientUserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $slug = 'client-users';

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Clients';
    protected static ?int $navigationSort = 5;
    protected static ?string $modelLabel = 'Client User';
    protected static ?string $pluralModelLabel = 'Users & Permissions';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereIn('role', ['client', 'client_staff']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Client User Details')->schema([
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
                    Forms\Components\Select::make('role')
                        ->options([
                            'client' => 'Main Client (Account Owner)',
                            'client_staff' => 'Client Staff',
                        ])
                        ->required()
                        ->live(),
                    Forms\Components\Select::make('plan_id')
                        ->label('Subscription Plan')
                        ->relationship('plan', 'name')
                        ->searchable()
                        ->preload()
                        ->visible(fn (Forms\Get $get) => $get('role') === 'client'),
                    Forms\Components\Select::make('parent_id')
                        ->label('Main User (Parent Account)')
                        ->relationship('parent', 'name', fn (Builder $query) => $query->where('role', 'client'))
                        ->visible(fn (Forms\Get $get) => $get('role') === 'client_staff')
                        ->searchable()
                        ->preload(),
                    Forms\Components\Select::make('roles')
                        ->relationship('roles', 'name')
                        ->multiple()
                        ->preload()
                        ->searchable(),
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
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('role')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'client' ? 'info' : 'warning')
                    ->formatStateUsing(fn (string $state): string => str_replace('_', ' ', Str::title($state))),
                Tables\Columns\TextColumn::make('plan.name')
                    ->label('Plan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('parent.name')
                    ->label('Owner Account')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->options([
                        'client' => 'Main Client',
                        'client_staff' => 'Client Staff',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClientUsers::route('/'),
            'create' => Pages\CreateClientUser::route('/create'),
            'edit' => Pages\EditClientUser::route('/{record}/edit'),
        ];
    }
}
