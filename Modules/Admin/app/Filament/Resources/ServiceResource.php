<?php

namespace Modules\Admin\Filament\Resources;

use Modules\Admin\Filament\Resources\ServiceResource\Pages;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?string $navigationGroup = 'Marketplace';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'Standardized Service';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Service Details')->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Name (EN)')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('name_ar')
                        ->label('Name (AR)')
                        ->maxLength(255),
                    Forms\Components\Select::make('service_category_id')
                        ->label('Category')
                        ->relationship('serviceCategory', 'name')
                        ->required()
                        ->searchable()
                        ->preload(),
                    Forms\Components\TextInput::make('icon')
                        ->maxLength(255),
                    Forms\Components\Toggle::make('is_active')
                        ->default(true),
                    Forms\Components\RichEditor::make('description')
                        ->label('Description (EN)')
                        ->required()
                        ->columnSpanFull(),
                    Forms\Components\RichEditor::make('description_ar')
                        ->label('Description (AR)')
                        ->columnSpanFull(),
                ])->columns(2),

                Forms\Components\Section::make('Standardized Checklist')->schema([
                    Forms\Components\TagsInput::make('subtasks')
                        ->placeholder('Add subtask and press Enter')
                        ->columnSpanFull(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
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
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}
