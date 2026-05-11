<?php

namespace Modules\Admin\Filament\Resources;

use Modules\Admin\Filament\Resources\ProjectHistoryResource\Pages;
use Modules\Admin\Filament\Resources\ProjectHistoryResource\RelationManagers;
use App\Models\ProjectHistory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProjectHistoryResource extends Resource
{
    protected static ?string $model = ProjectHistory::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationGroup = 'Audit Logs';
    protected static ?int $navigationSort = 1;

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Project History Detail')
                    ->schema([
                        Infolists\Components\TextEntry::make('project.id')->label('Project ID'),
                        Infolists\Components\TextEntry::make('project.service.name')->label('Service'),
                        Infolists\Components\TextEntry::make('user.name')->label('User'),
                        Infolists\Components\TextEntry::make('action')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'created' => 'success',
                                'updated' => 'info',
                                'deleted' => 'danger',
                                default => 'gray',
                            }),
                        Infolists\Components\TextEntry::make('description')->columnSpanFull(),
                        Infolists\Components\TextEntry::make('created_at')->dateTime(),
                    ])->columns(3),

                Infolists\Components\Section::make('Full Project History')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('project.histories')
                            ->label('')
                            ->schema([
                                Infolists\Components\TextEntry::make('user.name')->label('User'),
                                Infolists\Components\TextEntry::make('action')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'created' => 'success',
                                        'updated' => 'info',
                                        'deleted' => 'danger',
                                        default => 'gray',
                                    }),
                                Infolists\Components\TextEntry::make('description'),
                                Infolists\Components\TextEntry::make('created_at')->dateTime()->label('Time'),
                            ])
                            ->columns(4)
                            ->grid(1)
                    ])
            ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('project_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('user_id')
                    ->numeric(),
                Forms\Components\TextInput::make('action')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('metadata'),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereIn('id', function ($query) {
                $query->selectRaw('MAX(id)')
                    ->from('project_histories')
                    ->groupBy('project_id', 'user_id');
            });
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('project.id')
                    ->label('Project ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('project.service.name')
                    ->label('Service')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Last Action By')
                    ->searchable(),
                Tables\Columns\TextColumn::make('action')
                    ->label('Latest Action')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'info',
                        'deleted' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('description')
                    ->label('Latest Description')
                    ->limit(50),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Last Updated')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('project')
                    ->relationship('project', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "Project #{$record->id} - " . ($record->service->name ?? 'Unknown Service')),
                Tables\Filters\SelectFilter::make('user')
                    ->relationship('user', 'name'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('View Full History'),
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
            'index' => Pages\ListProjectHistories::route('/'),
            'create' => Pages\CreateProjectHistory::route('/create'),
            'view' => Pages\ViewProjectHistory::route('/{record}'),
            'edit' => Pages\EditProjectHistory::route('/{record}/edit'),
        ];
    }
}
