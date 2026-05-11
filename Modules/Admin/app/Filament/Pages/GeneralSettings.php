<?php

namespace Modules\Admin\Filament\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use App\Settings\GeneralSettings as GeneralSettingsModel;

class GeneralSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'System Settings';
    protected static ?int $navigationSort = 2;
    protected static string $settings = GeneralSettingsModel::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Branding & Logos')->schema([
                    Forms\Components\FileUpload::make('logo')
                        ->label('Main Logo')
                        ->directory('settings')
                        ->image()
                        ->previewable()
                        ->required(),
                    Forms\Components\Placeholder::make('current_logo')
                        ->label('Current Main Logo')
                        ->content(function ($record, $get) {
                            $logo = $get('logo');
                            if (is_array($logo)) $logo = collect($logo)->first();
                            if (!$logo || !is_string($logo)) return 'No logo set';
                            $url = str_starts_with($logo, 'settings/') ? asset('storage/' . $logo) : asset($logo);
                            return new \Illuminate\Support\HtmlString("<img src=\"{$url}\" style=\"height: 50px;\" class=\"object-contain\">");
                        }),
                    Forms\Components\FileUpload::make('collapsed_logo')
                        ->label('Collapsed Logo (Icon)')
                        ->directory('settings')
                        ->image()
                        ->previewable()
                        ->required(),
                    Forms\Components\Placeholder::make('current_collapsed_logo')
                        ->label('Current Collapsed Logo')
                        ->content(function ($record, $get) {
                            $logo = $get('collapsed_logo');
                            if (is_array($logo)) $logo = collect($logo)->first();
                            if (!$logo || !is_string($logo)) return 'No logo set';
                            $url = str_starts_with($logo, 'settings/') ? asset('storage/' . $logo) : asset($logo);
                            return new \Illuminate\Support\HtmlString("<img src=\"{$url}\" style=\"height: 50px;\" class=\"object-contain\">");
                        }),
                ])->columns(2),

                Forms\Components\Section::make('General Configuration')->schema([
                    Forms\Components\TextInput::make('site_name')
                        ->label('Site Name')
                        ->required(),
                    Forms\Components\TextInput::make('contact_email')
                        ->label('Contact Email')
                        ->email(),
                    Forms\Components\TextInput::make('contact_phone')
                        ->label('Contact Phone'),
                    Forms\Components\Select::make('default_currency')
                        ->label('Default Currency')
                        ->options([
                            'SAR' => 'Saudi Riyal (SAR)',
                            'USD' => 'US Dollar (USD)',
                        ])
                        ->required(),
                    Forms\Components\TextInput::make('platform_fee_percentage')
                        ->label('Platform Fee (%)')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->required(),
                    Forms\Components\Select::make('default_language')
                        ->label('Default Language')
                        ->options([
                            'en' => 'English',
                            'ar' => 'Arabic',
                        ])
                        ->required(),
                ])->columns(2),

                Forms\Components\Section::make('Global Protection Block')->schema([
                    Forms\Components\TextInput::make('protection_block_title')
                        ->label('Title')
                        ->required(),
                    Forms\Components\ColorPicker::make('protection_block_bg_color')
                        ->label('Background Color'),
                    Forms\Components\Textarea::make('protection_block_description')
                        ->label('Description')
                        ->columnSpanFull()
                        ->required(),
                    Forms\Components\Repeater::make('protection_block_points')
                        ->label('Sub-points')
                        ->schema([
                            Forms\Components\TextInput::make('icon')
                                ->label('Lucide Icon Name')
                                ->required(),
                            Forms\Components\TextInput::make('text')
                                ->label('Text')
                                ->required(),
                        ])
                        ->grid(2)
                        ->columnSpanFull(),
                ])->columns(2),
            ]);
    }
}
