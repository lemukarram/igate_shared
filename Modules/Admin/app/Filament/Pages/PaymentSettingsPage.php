<?php

namespace Modules\Admin\Filament\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use App\Settings\PaymentSettings;

class PaymentSettingsPage extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationGroup = 'Financials';
    protected static ?int $navigationSort = 100;
    protected static string $settings = PaymentSettings::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Payment Status Customization')
                    ->description('Manage how payment statuses are named and displayed across the platform.')
                    ->schema([
                        Forms\Components\TextInput::make('status_pending_label')
                            ->label('Pending Status Label')
                            ->required(),
                        Forms\Components\TextInput::make('status_authorized_label')
                            ->label('Authorized (Escrow) Status Label')
                            ->required(),
                        Forms\Components\TextInput::make('status_captured_label')
                            ->label('Captured Status Label')
                            ->required(),
                        Forms\Components\TextInput::make('status_failed_label')
                            ->label('Failed / Declined Status Label')
                            ->required(),
                        Forms\Components\TextInput::make('status_refunded_label')
                            ->label('Refunded Status Label')
                            ->required(),
                        Forms\Components\TextInput::make('status_void_label')
                            ->label('Voided Status Label')
                            ->required(),
                        Forms\Components\TextInput::make('status_cancelled_label')
                            ->label('Cancelled Status Label')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Tap Payments Configuration')->schema([
                    Forms\Components\Select::make('environment')
                        ->label('Environment')
                        ->options([
                            'sandbox' => 'Sandbox (Test Mode)',
                            'live' => 'Live (Production Mode)',
                        ])
                        ->required()
                        ->live(),

                    Forms\Components\TextInput::make('merchant_id')
                        ->label('Merchant ID')
                        ->required(),
                        
                    Forms\Components\TextInput::make('webhook_secret')
                        ->label('Webhook Secret (for signature verification)')
                        ->password()
                        ->revealable()
                        ->required(),
                ])->columns(2),

                Forms\Components\Section::make('API Keys')
                    ->description('Enter the secret keys provided by Tap Payments.')
                    ->schema([
                    Forms\Components\TextInput::make('sandbox_secret_key')
                        ->label('Sandbox Secret Key')
                        ->password()
                        ->revealable()
                        ->required(fn (Forms\Get $get) => $get('environment') === 'sandbox'),

                    Forms\Components\TextInput::make('live_secret_key')
                        ->label('Live Secret Key')
                        ->password()
                        ->revealable()
                        ->required(fn (Forms\Get $get) => $get('environment') === 'live'),
                ])->columns(2),

                Forms\Components\Section::make('Automated Workflows')
                    ->description('Configure how the system handles payment captures and automation.')
                    ->schema([
                        Forms\Components\Select::make('auto_capture_days')
                            ->label('Auto-Capture Delay')
                            ->options([
                                1 => '1 Day',
                                2 => '2 Days',
                                3 => '3 Days',
                                4 => '4 Days',
                                5 => '5 Days',
                                6 => '6 Days',
                                7 => '7 Days',
                            ])
                            ->helperText('For Service Escrow: Number of days to wait before automatically capturing authorized funds.')
                            ->required(),
                    ]),

                Forms\Components\Section::make('Success Page Content')
                    ->description('Customize the message shown to clients after a successful payment.')
                    ->schema([
                        Forms\Components\TextInput::make('success_title')
                            ->label('Success Title')
                            ->placeholder('Payment Successful!')
                            ->required(),
                        Forms\Components\Textarea::make('success_message')
                            ->label('Success Message')
                            ->placeholder('Your transaction has been confirmed. Our team is now reviewing the request. Once approved, your project workspace will become active shortly.')
                            ->rows(4)
                            ->required(),
                    ]),

                Forms\Components\Section::make('Payment Method Logos')
                    ->description('Upload logos for different payment methods displayed at checkout.')
                    ->schema([
                        Forms\Components\FileUpload::make('tap_logo')
                            ->label('Tap Pay Logo')
                            ->directory('settings/payments')
                            ->image()
                            ->previewable(),
                        Forms\Components\FileUpload::make('visa_logo')
                            ->label('Visa Logo')
                            ->directory('settings/payments')
                            ->image()
                            ->previewable(),
                        Forms\Components\FileUpload::make('mastercard_logo')
                            ->label('Mastercard Logo')
                            ->directory('settings/payments')
                            ->image()
                            ->previewable(),
                        Forms\Components\FileUpload::make('mada_logo')
                            ->label('Mada Logo')
                            ->directory('settings/payments')
                            ->image()
                            ->previewable(),
                    ])->columns(3),
            ]);
    }
}
