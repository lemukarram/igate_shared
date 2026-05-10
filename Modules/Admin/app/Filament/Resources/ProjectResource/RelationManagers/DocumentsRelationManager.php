<?php

namespace Modules\Admin\Filament\Resources\ProjectResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\FileUpload::make('file_path')
                    ->required()
                    ->label('File')
                    ->directory('project-documents')
                    ->preserveFilenames()
                    ->openable()
                    ->downloadable(),
                Forms\Components\Select::make('user_id')
                    ->label('Uploaded By')
                    ->relationship('user', 'name')
                    ->default(auth()->id())
                    ->required(),
                Forms\Components\Toggle::make('is_private')
                    ->label('Private Document'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Uploaded By'),
                Tables\Columns\TextColumn::make('file_type')
                    ->badge(),
                Tables\Columns\IconColumn::make('is_private')
                    ->boolean()
                    ->label('Private'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
