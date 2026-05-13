<?php

namespace Modules\Admin\Filament\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use App\Settings\PaymentSettings;

class PaymentSettingsPage extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationGroup = 'System Settings';
    protected static ?int $navigationSort = 3;
    protected static string $settings = PaymentSettings::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
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
            ]);
    }
}
