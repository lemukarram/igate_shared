<?php

namespace Modules\Admin\Filament\Resources;

use Modules\Admin\Filament\Resources\TransactionResource\Pages;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\Payments\Services\TapPaymentService;
use Filament\Notifications\Notification;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';
    protected static ?string $navigationGroup = 'Financials';
    protected static ?int $navigationSort = 2;
    protected static ?string $modelLabel = 'Tap Transaction';
    protected static ?string $pluralModelLabel = 'Tap Transactions';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Transaction Details')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('id')
                                    ->label('Internal Reference')
                                    ->disabled(),
                                Forms\Components\TextInput::make('tap_charge_id')
                                    ->label('Tap Gateway ID')
                                    ->disabled(),
                                Forms\Components\TextInput::make('status')
                                    ->disabled(),
                                Forms\Components\TextInput::make('amount')
                                    ->suffix('SAR')
                                    ->disabled(),
                                Forms\Components\TextInput::make('currency')
                                    ->disabled(),
                                Forms\Components\TextInput::make('type')
                                    ->disabled(),
                            ]),
                    ]),

                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\Section::make('Client Information')
                            ->schema([
                                Forms\Components\Placeholder::make('client_name')
                                    ->label('Name')
                                    ->content(fn (Transaction $record): string => $record->user->name ?? 'N/A'),
                                Forms\Components\Placeholder::make('client_email')
                                    ->label('Email')
                                    ->content(fn (Transaction $record): string => $record->user->email ?? 'N/A'),
                            ])->columnSpan(1),

                        Forms\Components\Section::make('Provider Information')
                            ->schema([
                                Forms\Components\Placeholder::make('provider_name')
                                    ->label('Name')
                                    ->content(fn (Transaction $record): string => $record->provider->name ?? 'N/A'),
                                Forms\Components\Placeholder::make('provider_company')
                                    ->label('Company')
                                    ->content(fn (Transaction $record): string => $record->provider->providerProfile->company_name ?? 'N/A'),
                            ])->columnSpan(1),
                    ]),

                Forms\Components\Section::make('Related Service / Project')
                    ->schema([
                        Forms\Components\Placeholder::make('service_name')
                            ->label('Standardized Service')
                            ->content(fn (Transaction $record): string => $record->project->service->name ?? 'Subscription'),
                        Forms\Components\Placeholder::make('project_status')
                            ->label('Project Current Status')
                            ->content(fn (Transaction $record): string => strtoupper($record->project->status ?? 'N/A')),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tap_charge_id')
                    ->label('Tap ID')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('project.service.name')
                    ->label('Service')
                    ->searchable()
                    ->sortable()
                    ->default('Subscription'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('provider.name')
                    ->label('Provider')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->money('SAR')
                    ->sortable()
                    ->alignment('right'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'authorized' => 'warning',
                        'captured' => 'success',
                        'failed' => 'danger',
                        'refunded' => 'info',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'authorized' => 'Authorized (Escrow)',
                        'captured' => 'Captured',
                        'failed' => 'Failed',
                        'refunded' => 'Refunded',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('sync_status')
                    ->label('Sync with Tap')
                    ->icon('heroicon-o-arrow-path')
                    ->color('info')
                    ->action(function (Transaction $record) {
                        try {
                            $tapService = app(TapPaymentService::class);
                            $charge = $tapService->getCharge($record->tap_charge_id);
                            $tapStatus = strtolower($charge['status'] ?? '');
                            $newTapId = $charge['id'] ?? $record->tap_charge_id;

                            $mappedStatus = match ($tapStatus) {
                                'captured' => 'captured',
                                'authorized' => 'authorized',
                                'declined', 'failed', 'cancelled' => 'failed',
                                'refunded' => 'refunded',
                                default => $record->status,
                            };

                            $updateData = ['status' => $mappedStatus];
                            if ($newTapId !== $record->tap_charge_id) {
                                $updateData['tap_charge_id'] = $newTapId;
                            }

                            if ($mappedStatus !== $record->status || isset($updateData['tap_charge_id'])) {
                                $record->update($updateData);

                                // Handle project activation if it was pending
                                if ($record->project_id && in_array($mappedStatus, ['captured', 'authorized'])) {
                                    $project = $record->project;
                                    if ($project->status === 'pending_payment') {
                                        $project->update(['status' => 'awaiting_approval']);
                                        
                                        \App\Models\ProjectHistory::create([
                                            'project_id' => $project->id,
                                            'user_id' => auth()->id(),
                                            'action' => 'payment_synced',
                                            'description' => "Payment status synced manually from Tap: {$tapStatus}. Project awaiting approval.",
                                        ]);

                                        // Ensure payment record exists
                                        \App\Models\Payment::updateOrCreate(
                                            ['transaction_id' => $record->tap_charge_id],
                                            [
                                                'project_id' => $project->id,
                                                'user_id' => $record->user_id,
                                                'amount' => $record->amount,
                                                'payment_method' => 'tap',
                                                'status' => 'held_in_escrow',
                                            ]
                                        );
                                    }
                                }
                            }

                            Notification::make()
                                ->title('Status Synced')
                                ->body("Current Tap Status: " . strtoupper($tapStatus))
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Sync Failed')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Tables\Actions\Action::make('approve_project')
                    ->label('Approve Project')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Transaction $record) => 
                        $record->project_id && 
                        $record->project->status === 'awaiting_approval' && 
                        in_array($record->status, ['authorized', 'captured'])
                    )
                    ->action(function (Transaction $record) {
                        $record->project->update(['status' => 'active']);
                        
                        \App\Models\ProjectHistory::create([
                            'project_id' => $record->project_id,
                            'user_id' => auth()->id(),
                            'action' => 'project_approved',
                            'description' => 'Project manually approved by admin. Now active.',
                        ]);

                        Notification::make()
                            ->title('Project Approved')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('capture')
                    ->label('Capture Funds')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Capture Authorized Funds')
                    ->modalDescription('Are you sure you want to capture these funds from Tap? This action cannot be undone.')
                    ->visible(fn (Transaction $record) => $record->status === 'authorized')
                    ->action(function (Transaction $record) {
                        try {
                            $tapService = app(TapPaymentService::class);
                            $response = $tapService->captureAuthorizedFunds($record->tap_charge_id, $record->amount);
                            
                            $newTapId = $response['id'] ?? $record->tap_charge_id;

                            $record->update([
                                'status' => 'captured',
                                'tap_charge_id' => $newTapId
                            ]);

                            if ($record->project_id) {
                                \Illuminate\Support\Facades\DB::table('payments')
                                    ->where('transaction_id', $record->tap_charge_id)
                                    ->orWhere('transaction_id', $newTapId)
                                    ->update([
                                        'status' => 'released',
                                        'transaction_id' => $newTapId
                                    ]);
                            }

                            Notification::make()
                                ->title('Funds Captured Successfully')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Failed to capture funds')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Tables\Actions\Action::make('refund')
                    ->label('Refund')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\TextInput::make('reason')
                            ->label('Refund Reason')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->modalHeading('Refund Transaction')
                    ->modalDescription('Are you sure you want to refund this transaction? This will return funds to the customer.')
                    ->visible(fn (Transaction $record) => $record->status === 'captured')
                    ->action(function (Transaction $record, array $data) {
                        try {
                            $tapService = app(TapPaymentService::class);
                            $response = $tapService->refundCharge($record->tap_charge_id, $record->amount, $data['reason']);
                            
                            $record->update(['status' => 'refunded']);

                            if ($record->project_id) {
                                \Illuminate\Support\Facades\DB::table('payments')
                                    ->where('transaction_id', $record->tap_charge_id)
                                    ->update(['status' => 'refunded']);
                                    
                                $record->project->update(['status' => 'cancelled']);
                            }

                            Notification::make()
                                ->title('Funds Refunded Successfully')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Failed to refund funds')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                //
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getWidgets(): array
    {
        return [
            \Modules\Admin\Filament\Resources\TransactionResource\Widgets\FinancialStats::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'view' => Pages\ViewTransaction::route('/{record}'),
        ];
    }
}
