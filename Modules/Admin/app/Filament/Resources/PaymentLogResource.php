<?php

namespace Modules\Admin\Filament\Resources;

use Modules\Admin\Filament\Resources\PaymentLogResource\Pages;
use App\Models\PaymentLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

use Filament\Infolists;
use Filament\Infolists\Infolist;

class PaymentLogResource extends Resource
{
    protected static ?string $model = PaymentLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';
    protected static ?string $navigationGroup = 'Financials';
    protected static ?int $navigationSort = 4;
    protected static ?string $modelLabel = 'Payment Log';
    protected static ?string $pluralModelLabel = 'Payment Logs';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // The form is used for basic layout, but we will prioritize Infolist for viewing
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('General Information')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('type')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'api_call' => 'info',
                                        'webhook_received' => 'warning',
                                        default => 'gray',
                                    }),
                                Infolists\Components\TextEntry::make('event')
                                    ->placeholder('-'),
                                Infolists\Components\TextEntry::make('status_code')
                                    ->badge()
                                    ->color(fn ($state) => $state >= 400 ? 'danger' : 'success'),
                                Infolists\Components\TextEntry::make('method'),
                                Infolists\Components\TextEntry::make('endpoint'),
                                Infolists\Components\TextEntry::make('ip_address'),
                            ]),
                    ]),
                Infolists\Components\Grid::make(2)
                    ->schema([
                        Infolists\Components\Section::make('Payload (Request Data)')
                            ->schema([
                                Infolists\Components\TextEntry::make('payload')
                                    ->label('')
                                    ->html()
                                    ->state(function ($record) {
                                        $state = $record->payload;
                                        if (!$state) return '<span class="text-gray-400 italic">Empty</span>';
                                        $json = json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                                        return '<pre class="text-xs bg-gray-900 text-green-400 p-4 rounded-xl overflow-x-auto"><code>' . e($json) . '</code></pre>';
                                    }),
                            ])->columnSpan(1),
                        Infolists\Components\Section::make('Response Body / Webhook Data')
                            ->schema([
                                Infolists\Components\TextEntry::make('response_body')
                                    ->label('')
                                    ->html()
                                    ->state(function ($record) {
                                        $state = $record->response_body;
                                        if (!$state) return '<span class="text-gray-400 italic">Empty</span>';
                                        $json = json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                                        return '<pre class="text-xs bg-gray-900 text-blue-400 p-4 rounded-xl overflow-x-auto"><code>' . e($json) . '</code></pre>';
                                    }),
                            ])->columnSpan(1),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Timestamp')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'api_call' => 'info',
                        'webhook_received' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('event')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('method')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('endpoint')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status_code')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ip_address')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'api_call' => 'API Call',
                        'webhook_received' => 'Webhook',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                //
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListPaymentLogs::route('/'),
        ];
    }
}
