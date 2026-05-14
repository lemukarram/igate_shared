<?php

namespace Modules\Admin\Filament\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use App\Settings\InvoiceSettings as InvoiceSettingsModel;

class InvoiceSettingsPage extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'System Settings';
    protected static ?int $navigationSort = 4;
    protected static string $settings = InvoiceSettingsModel::class;
    protected static ?string $title = 'Invoice Settings';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Invoice Branding')->schema([
                    Forms\Components\FileUpload::make('logo')
                        ->label('Invoice Logo')
                        ->directory('settings')
                        ->image()
                        ->previewable(),
                    Forms\Components\TextInput::make('invoice_prefix')
                        ->label('Invoice Number Prefix')
                        ->placeholder('IGATE-')
                        ->required(),
                ])->columns(2),

                Forms\Components\Section::make('Platform Details')->schema([
                    Forms\Components\TextInput::make('company_name')
                        ->label('Company Name')
                        ->placeholder('iGate Shared Services')
                        ->required(),
                    Forms\Components\Textarea::make('address')
                        ->label('Platform Address')
                        ->required(),
                    Forms\Components\TextInput::make('tax_id')
                        ->label('Tax ID / CR Number')
                        ->required(),
                    Forms\Components\TextInput::make('contact_info')
                        ->label('Contact Info (Email/Phone)')
                        ->required(),
                ])->columns(1),

                Forms\Components\Section::make('Legal & Footer')->schema([
                    Forms\Components\Textarea::make('thank_you_note')
                        ->label('Thank You Note')
                        ->rows(3),
                    Forms\Components\Textarea::make('terms_conditions')
                        ->label('Terms & Conditions')
                        ->rows(5),
                ])->columns(1),
            ]);
    }
}
