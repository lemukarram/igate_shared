<?php

namespace Modules\Admin\Filament\Resources;

use Modules\Admin\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\Action;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Identity & Organizations';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('User Details')->schema([
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
                            'admin' => 'Admin',
                            'client' => 'Main Client',
                            'provider' => 'Main Provider',
                            'client_staff' => 'Client Staff',
                            'provider_staff' => 'Provider Staff',
                        ])
                        ->required()
                        ->live(),
                    Forms\Components\Select::make('plan_id')
                        ->label('Subscription Plan')
                        ->relationship('plan', 'name')
                        ->searchable()
                        ->preload()
                        ->visible(fn (Forms\Get $get) => in_array($get('role'), ['client', 'provider'])),
                    Forms\Components\Select::make('parent_id')
                        ->label('Parent Organization / Main User')
                        ->relationship('parent', 'name', fn (Builder $query, Forms\Get $get) => 
                            $query->where('role', str_replace('_staff', '', $get('role')))
                        )
                        ->visible(fn (Forms\Get $get) => in_array($get('role'), ['client_staff', 'provider_staff']))
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
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'client' => 'info',
                        'provider' => 'success',
                        'client_staff' => 'warning',
                        'provider_staff' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => str_replace('_', ' ', Str::title($state)))
                    ->searchable(),
                Tables\Columns\TextColumn::make('parent.name')
                    ->label('Parent Org')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->options([
                        'admin' => 'Admin',
                        'client' => 'Main Client',
                        'provider' => 'Main Provider',
                        'client_staff' => 'Client Staff',
                        'provider_staff' => 'Provider Staff',
                    ]),
            ])
            ->actions([
                Tables\Columns\Layout\Split::make([
                    Tables\Actions\EditAction::make(),
                    Action::make('impersonate')
                        ->icon('heroicon-o-eye')
                        ->color('gray')
                        ->action(fn (User $record) => auth()->login($record))
                        ->requiresConfirmation()
                        ->modalHeading('Impersonate User')
                        ->modalDescription('Are you sure you want to login as this user?'),
                ]),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
