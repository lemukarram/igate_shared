<?php

namespace Modules\Admin\Filament\Resources;

use Modules\Admin\Filament\Resources\SubscriptionResource\Pages;
use App\Models\Subscription;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationGroup = 'Financials';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Subscription Details')->schema([
                    Forms\Components\Select::make('client_id')
                        ->relationship('client', 'name', fn (Builder $query) => $query->where('role', 'client'))
                        ->required()
                        ->searchable()
                        ->preload(),
                    Forms\Components\Select::make('company_id')
                        ->relationship('company', 'name')
                        ->required()
                        ->searchable()
                        ->preload(),
                    Forms\Components\Select::make('service_id')
                        ->relationship('service', 'name')
                        ->required()
                        ->searchable()
                        ->preload(),
                    Forms\Components\Select::make('provider_id')
                        ->relationship('provider', 'name', fn (Builder $query) => $query->where('role', 'provider'))
                        ->searchable()
                        ->preload(),
                    Forms\Components\Select::make('plan_id')
                        ->relationship('plan', 'name')
                        ->searchable()
                        ->preload(),
                    Forms\Components\TextInput::make('plan_name')
                        ->maxLength(255),
                    Forms\Components\Select::make('billing_cycle')
                        ->options([
                            'monthly' => 'Monthly',
                            'annually' => 'Annually',
                        ])
                        ->default('monthly')
                        ->required(),
                    Forms\Components\Select::make('status')
                        ->options([
                            'active' => 'Active',
                            'cancelled' => 'Cancelled',
                            'expired' => 'Expired',
                            'pending' => 'Pending',
                        ])
                        ->default('active')
                        ->required(),
                    Forms\Components\DateTimePicker::make('starts_at'),
                    Forms\Components\DateTimePicker::make('ends_at'),
                    Forms\Components\DateTimePicker::make('next_billing_date'),
                    Forms\Components\DateTimePicker::make('canceled_at'),
                    Forms\Components\TextInput::make('card_token')
                        ->disabled(),
                    Forms\Components\TextInput::make('tap_customer_id')
                        ->disabled(),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('company.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('service.name')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Platform Plan'),
                Tables\Columns\TextColumn::make('plan.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'cancelled' => 'danger',
                        'expired' => 'warning',
                        'pending' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('billing_cycle'),
                Tables\Columns\TextColumn::make('next_billing_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('starts_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('ends_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'cancelled' => 'Cancelled',
                        'expired' => 'Expired',
                        'pending' => 'Pending',
                    ]),
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
            'index' => Pages\ListSubscriptions::route('/'),
            'create' => Pages\CreateSubscription::route('/create'),
            'edit' => Pages\EditSubscription::route('/{record}/edit'),
        ];
    }
}
