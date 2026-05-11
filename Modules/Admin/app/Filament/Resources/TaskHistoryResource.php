<?php

namespace Modules\Admin\Filament\Resources;

use Modules\Admin\Filament\Resources\TaskHistoryResource\Pages;
use Modules\Admin\Filament\Resources\TaskHistoryResource\RelationManagers;
use App\Models\TaskHistory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TaskHistoryResource extends Resource
{
    protected static ?string $model = TaskHistory::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';
    protected static ?string $navigationGroup = 'Audit Logs';
    protected static ?int $navigationSort = 2;

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Task History Detail')
                    ->schema([
                        Infolists\Components\TextEntry::make('task.id')->label('Task ID'),
                        Infolists\Components\TextEntry::make('task.title')->label('Task Title'),
                        Infolists\Components\TextEntry::make('user.name')->label('User'),
                        Infolists\Components\TextEntry::make('field'),
                        Infolists\Components\TextEntry::make('action')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'created' => 'success',
                                'updated' => 'info',
                                'deleted' => 'danger',
                                default => 'gray',
                            }),
                        Infolists\Components\TextEntry::make('old_value')->columnSpanFull(),
                        Infolists\Components\TextEntry::make('new_value')->columnSpanFull(),
                        Infolists\Components\TextEntry::make('created_at')->dateTime(),
                    ])->columns(3),

                Infolists\Components\Section::make('Full Task History')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('task.histories')
                            ->label('')
                            ->schema([
                                Infolists\Components\TextEntry::make('user.name')->label('User'),
                                Infolists\Components\TextEntry::make('field'),
                                Infolists\Components\TextEntry::make('action')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'created' => 'success',
                                        'updated' => 'info',
                                        'deleted' => 'danger',
                                        default => 'gray',
                                    }),
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
                Forms\Components\TextInput::make('task_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('field')
                    ->maxLength(255),
                Forms\Components\Textarea::make('old_value')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('new_value')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('action')
                    ->maxLength(255),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereIn('id', function ($query) {
                $query->selectRaw('MAX(id)')
                    ->from('task_histories')
                    ->groupBy('task_id');
            });
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('task.id')
                    ->label('Task ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('task.title')
                    ->label('Task Title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Last Action By')
                    ->searchable(),
                Tables\Columns\TextColumn::make('field')
                    ->label('Field Updated'),
                Tables\Columns\TextColumn::make('action')
                    ->label('Latest Action')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'info',
                        'deleted' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Last Updated')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('task')
                    ->relationship('task', 'title'),
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
            'index' => Pages\ListTaskHistories::route('/'),
            'create' => Pages\CreateTaskHistory::route('/create'),
            'view' => Pages\ViewTaskHistory::route('/{record}'),
            'edit' => Pages\EditTaskHistory::route('/{record}/edit'),
        ];
    }
}
