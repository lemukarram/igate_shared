<?php

namespace Modules\Admin\Filament\Resources;

use Modules\Admin\Filament\Resources\PreSaleMessageResource\Pages;
use Modules\Admin\Filament\Resources\PreSaleMessageResource\RelationManagers;
use App\Models\PreSaleMessage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group;
use Filament\Support\Enums\FontWeight;

class PreSaleMessageResource extends Resource
{
    protected static ?string $model = PreSaleMessage::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationGroup = 'Service Provider';
    protected static ?int $navigationSort = 3;
    protected static ?string $modelLabel = 'Pre Sale Chat';
    protected static ?string $pluralModelLabel = 'Pre Sale Chats';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Inquiry Details')
                    ->schema([
                        Forms\Components\Select::make('client_id')
                            ->relationship('client', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('provider_id')
                            ->relationship('provider', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('service_id')
                            ->relationship('service', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('sender_id')
                            ->relationship('sender', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Textarea::make('message')
                            ->required()
                            ->columnSpanFull()
                            ->rows(5),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->select('pre_sale_messages.*')
                ->join(\DB::raw('(SELECT MAX(id) as max_id FROM pre_sale_messages GROUP BY client_id, provider_id, service_id) as sub'), 'pre_sale_messages.id', '=', 'sub.max_id')
            )
            ->columns([
                Tables\Columns\TextColumn::make('client.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('provider.name')
                    ->label('Provider')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('service.name')
                    ->label('Service')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('message')
                    ->label('Last Message')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Last Activity')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema(fn (PreSaleMessage $record): array => [
                Section::make('Conversation Overview')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('client.name')->label('Client'),
                                TextEntry::make('provider.name')->label('Provider'),
                                TextEntry::make('service.name')->label('Service'),
                            ]),
                    ]),
                Section::make('Conversation Thread')
                    ->schema([
                        RepeatableEntry::make('conversation_thread')
                            ->label('')
                            ->getStateUsing(fn (PreSaleMessage $record) => PreSaleMessage::where('client_id', $record->client_id)
                                ->where('provider_id', $record->provider_id)
                                ->where('service_id', $record->service_id)
                                ->orderBy('created_at', 'asc')
                                ->get()
                            )
                            ->schema([
                                Grid::make(12)
                                    ->schema([
                                        \Filament\Infolists\Components\Actions::make([
                                            \Filament\Infolists\Components\Actions\Action::make('delete_message')
                                                ->label('')
                                                ->icon('heroicon-m-trash')
                                                ->color('danger')
                                                ->requiresConfirmation()
                                                ->action(fn (PreSaleMessage $record) => $record->delete())
                                                ->after(fn () => redirect(request()->header('Referer'))),
                                        ])->columnSpan(1),
                                        TextEntry::make('formatted_message')
                                            ->label('')
                                            ->html()
                                            ->getStateUsing(fn (PreSaleMessage $record) => "
                                                <span style='font-weight: bold;'>{$record->sender?->name}</span>
                                                <span style='color: #6b7280; font-size: 0.75rem; margin-left: 4px;'>[{$record->created_at->format('M d, Y H:i')}]</span>
                                                <span style='margin-left: 4px;'>: {$record->message}</span>
                                            ")
                                            ->color(fn (PreSaleMessage $record) => $record->sender_id === $record->client_id ? 'primary' : 'info')
                                            ->columnSpan(11),
                                    ]),
                            ])
                            ->contained(false)
                            ->columnSpanFull(),
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
            'index' => Pages\ListPreSaleMessages::route('/'),
            'create' => Pages\CreatePreSaleMessage::route('/create'),
            'view' => Pages\ViewPreSaleMessage::route('/{record}'),
            'edit' => Pages\EditPreSaleMessage::route('/{record}/edit'),
        ];
    }
}
