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
            ]);
    }
}
