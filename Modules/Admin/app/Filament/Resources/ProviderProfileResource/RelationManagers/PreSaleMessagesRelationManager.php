<?php

namespace Modules\Admin\Filament\Resources\ProviderProfileResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\PreSaleMessage;
use Illuminate\Database\Eloquent\Builder;

class PreSaleMessagesRelationManager extends RelationManager
{
    protected static string $relationship = 'preSaleMessages';

    protected static ?string $title = 'Pre Sale Chat';

    protected static ?string $icon = 'heroicon-o-chat-bubble-left-right';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('client_id')
                    ->relationship('client', 'name')
                    ->required(),
                Forms\Components\Select::make('service_id')
                    ->relationship('service', 'name')
                    ->required(),
                Forms\Components\Textarea::make('message')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->select('pre_sale_messages.*')
                ->join(\DB::raw('(SELECT MAX(id) as max_id FROM pre_sale_messages GROUP BY client_id, provider_id, service_id) as sub'), 'pre_sale_messages.id', '=', 'sub.max_id')
            )
            ->columns([
                Tables\Columns\TextColumn::make('client.name')
                    ->label('Client')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('service.name')
                    ->label('Service')
                    ->sortable(),
                Tables\Columns\TextColumn::make('formatted_message')
                    ->label('Message')
                    ->html()
                    ->getStateUsing(fn (PreSaleMessage $record) => "
                        <span style='font-weight: bold;'>{$record->sender?->name}</span>
                        <span style='color: #6b7280; font-size: 0.75rem; margin-left: 4px;'>[{$record->created_at->format('M d, Y H:i')}]</span>
                        <span style='margin-left: 4px;'>: ({$record->message})</span>
                    ")
                    ->limit(100),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn (PreSaleMessage $record): string => "/admin/pre-sale-messages/{$record->id}"),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
