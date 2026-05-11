<?php

namespace Modules\Admin\Filament\Resources;

use Modules\Admin\Filament\Resources\ProviderServiceResource\Pages;
use Modules\Admin\Filament\Resources\ProviderServiceResource\RelationManagers;
use App\Models\ProviderService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProviderServiceResource extends Resource
{
    protected static ?string $model = ProviderService::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';
    protected static ?string $navigationGroup = 'Marketplace';
    protected static ?int $navigationSort = 3;
    protected static ?string $modelLabel = 'Marketplace Offering';
    protected static ?string $pluralModelLabel = 'Marketplace Offerings';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Offering Details')->schema([
                    Forms\Components\Select::make('provider_id')
                        ->label('Service Provider')
                        ->relationship(
                            name: 'provider',
                            titleAttribute: 'name',
                            modifyQueryUsing: fn (Builder $query) => $query->where('role', 'provider')
                        )
                        ->required()
                        ->searchable()
                        ->preload(),
                    Forms\Components\Select::make('service_id')
                        ->label('Standard Service')
                        ->relationship('service', 'name')
                        ->required()
                        ->searchable()
                        ->preload(),
                    Forms\Components\TextInput::make('price')
                        ->required()
                        ->numeric()
                        ->prefix('SAR'),
                    Forms\Components\TextInput::make('delivery_time_days')
                        ->label('Delivery Time (Days)')
                        ->required()
                        ->numeric(),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Active in Marketplace')
                        ->default(true),
                    Forms\Components\Textarea::make('provider_notes')
                        ->columnSpanFull(),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('provider.providerProfile.company_name')
                    ->label('Company')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('provider.name')
                    ->label('Owner')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('service.serviceCategory.name')
                    ->label('Category')
                    ->sortable(),
                Tables\Columns\TextColumn::make('service.name')
                    ->label('Service')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('SAR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('delivery_time_days')
                    ->label('Days')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('provider')
                    ->relationship('provider', 'name', fn (Builder $query) => $query->where('role', 'provider'))
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('category')
                    ->label('Category')
                    ->relationship('service.serviceCategory', 'name'),
                Tables\Filters\SelectFilter::make('service')
                    ->relationship('service', 'name')
                    ->searchable()
                    ->preload(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProviderServices::route('/'),
            'create' => Pages\CreateProviderService::route('/create'),
            'edit' => Pages\EditProviderService::route('/{record}/edit'),
        ];
    }
}
