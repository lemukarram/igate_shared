<?php

namespace Modules\Admin\Filament\Resources;

use Modules\Admin\Filament\Resources\PlanResource\Pages;
use Modules\Admin\Filament\Resources\PlanResource\RelationManagers;
use App\Models\Plan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PlanResource extends Resource
{
    protected static ?string $model = Plan::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationGroup = 'Marketplace';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Select::make('type')
                        ->options([
                            'client' => 'Client',
                            'provider' => 'Provider',
                        ])
                        ->required(),
                    Forms\Components\TextInput::make('monthly_price')
                        ->required()
                        ->numeric()
                        ->default(0.00)
                        ->prefix('SAR'),
                    Forms\Components\TextInput::make('annual_price')
                        ->required()
                        ->numeric()
                        ->default(0.00)
                        ->prefix('SAR'),
                    Forms\Components\Textarea::make('description')
                        ->columnSpanFull(),
                ])->columns(2),

                Forms\Components\Section::make('Limits')->schema([
                    Forms\Components\TextInput::make('max_services')
                        ->label('Max Services')
                        ->required()
                        ->numeric()
                        ->default(1),
                    Forms\Components\TextInput::make('max_users')
                        ->label('Max Users')
                        ->required()
                        ->numeric()
                        ->default(1),
                    Forms\Components\TextInput::make('max_projects')
                        ->label('Max Projects')
                        ->required()
                        ->numeric()
                        ->default(1),
                    Forms\Components\TextInput::make('max_companies')
                        ->label('Max Companies')
                        ->required()
                        ->numeric()
                        ->default(1)
                        ->visible(fn (Forms\Get $get) => $get('type') === 'client'),
                ])->columns(2),

                Forms\Components\Section::make('Features (Sub-points)')->schema([
                    Forms\Components\Repeater::make('features')
                        ->schema([
                            Forms\Components\TextInput::make('feature')
                                ->required(),
                        ])
                        ->columnSpanFull(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('monthly_price')
                    ->money('SAR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('annual_price')
                    ->money('SAR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_services')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_users')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_projects')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_companies')
                    ->numeric()
                    ->sortable(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlans::route('/'),
            'create' => Pages\CreatePlan::route('/create'),
            'edit' => Pages\EditPlan::route('/{record}/edit'),
        ];
    }
}
