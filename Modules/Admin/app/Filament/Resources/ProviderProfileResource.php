<?php

namespace Modules\Admin\Filament\Resources;

use Modules\Admin\Filament\Resources\ProviderProfileResource\Pages;
use Modules\Admin\Filament\Resources\ProviderProfileResource\RelationManagers;
use App\Models\ProviderProfile;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProviderProfileResource extends Resource
{
    protected static ?string $model = ProviderProfile::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $navigationGroup = 'Service Provider';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'Provider';
    protected static ?string $pluralModelLabel = 'Providers List';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('User Account Information')
                    ->relationship('user')
                    ->schema([
                        Forms\Components\FileUpload::make('profile_picture')
                            ->image()
                            ->avatar()
                            ->directory('profiles')
                            ->columnSpan(1),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('phone')
                                    ->tel()
                                    ->maxLength(255),
                                Forms\Components\Select::make('role')
                                    ->options([
                                        'provider' => 'Provider',
                                        'client' => 'Client',
                                        'admin' => 'Admin',
                                    ])
                                    ->required(),
                                Forms\Components\Select::make('plan_id')
                                    ->relationship(
                                        name: 'plan',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn (Builder $query) => $query->where('type', 'provider'),
                                    )
                                    ->searchable()
                                    ->preload(),
                            ])->columnSpan(2),
                    ])->columns(3),

                Forms\Components\Section::make('Agency Profile Details')
                    ->schema([
                        Forms\Components\FileUpload::make('logo')
                            ->image()
                            ->directory('logos')
                            ->columnSpan(1),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('company_name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('commercial_registration')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('tax_number')
                                    ->maxLength(255),
                                Forms\Components\Select::make('status')
                                    ->options([
                                        'pending' => 'Pending',
                                        'verified' => 'Verified',
                                        'rejected' => 'Rejected',
                                        'active' => 'Active',
                                        'inactive' => 'Inactive',
                                    ])
                                    ->required(),
                                Forms\Components\Toggle::make('onboarding_completed')
                                    ->required(),
                            ])->columnSpan(2),
                        Forms\Components\Textarea::make('bio')
                            ->columnSpanFull(),
                    ])->columns(3),

                Forms\Components\Section::make('Bank Information')
                    ->schema([
                        Forms\Components\TextInput::make('bank_name')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('iban')
                            ->maxLength(255),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('user.profile_picture')
                    ->label('Avatar')
                    ->circular(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Provider Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('company_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('commercial_registration')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tax_number')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('logo')
                    ->label('Logo'),
                Tables\Columns\TextColumn::make('bank_name')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('iban')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'verified' => 'info',
                        'pending' => 'warning',
                        'rejected' => 'danger',
                        'inactive' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('onboarding_completed')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
            RelationManagers\DocumentsRelationManager::class,
            RelationManagers\ProviderServicesRelationManager::class,
            RelationManagers\UsersRelationManager::class,
            RelationManagers\ProjectsRelationManager::class,
            RelationManagers\PreSaleMessagesRelationManager::class,
            RelationManagers\TasksRelationManager::class,
            RelationManagers\ReviewsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProviderProfiles::route('/'),
            'create' => Pages\CreateProviderProfile::route('/create'),
            'edit' => Pages\EditProviderProfile::route('/{record}/edit'),
        ];
    }
}
