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

                Forms\Components\Section::make('Contextual URLs')->schema([
                    Forms\Components\TextInput::make('services_url')
                        ->label('Services URL')
                        ->url()
                        ->required(),
                    Forms\Components\TextInput::make('finance_url')
                        ->label('Finance URL')
                        ->url()
                        ->required(),
                    Forms\Components\TextInput::make('enterprise_url')
                        ->label('Enterprise URL')
                        ->url()
                        ->required(),
                ])->columns(3),

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

                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\ColorPicker::make('protection_block_title_color')
                            ->label('Title Color'),
                        Forms\Components\TextInput::make('protection_block_title_size')
                            ->label('Title Font Size (e.g., 14px, 1rem)'),
                        Forms\Components\Select::make('protection_block_title_weight')
                            ->label('Title Font Weight')
                            ->options([
                                'font-light' => 'Light',
                                'font-normal' => 'Normal',
                                'font-medium' => 'Medium',
                                'font-semibold' => 'Semibold',
                                'font-bold' => 'Bold',
                            ]),
                    ]),

                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\ColorPicker::make('protection_block_description_color')
                            ->label('Description Color'),
                        Forms\Components\TextInput::make('protection_block_description_size')
                            ->label('Description Font Size'),
                    ]),

                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\ColorPicker::make('protection_block_points_text_color')
                            ->label('Points Text Color'),
                        Forms\Components\TextInput::make('protection_block_points_text_size')
                            ->label('Points Font Size'),
                        Forms\Components\ColorPicker::make('protection_block_icon_color')
                            ->label('Icon Color'),
                    ]),

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

                Forms\Components\Section::make('Recommended Services Block')->schema([
                    Forms\Components\Toggle::make('recommended_services_enabled')
                        ->label('Enable Recommended Services')
                        ->default(true),
                    Forms\Components\TextInput::make('recommended_services_title')
                        ->label('Section Title')
                        ->default('Recommended Services'),
                    Forms\Components\ColorPicker::make('recommended_services_bg_color')
                        ->label('Block Background Color'),
                    Forms\Components\ColorPicker::make('recommended_services_text_color')
                        ->label('Block Text Color'),
                    
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('recommended_services_heading_size')
                            ->label('Heading Font Size'),
                        Forms\Components\Select::make('recommended_services_heading_weight')
                            ->label('Heading Font Weight')
                            ->options([
                                'font-light' => 'Light',
                                'font-normal' => 'Normal',
                                'font-medium' => 'Medium',
                                'font-semibold' => 'Semibold',
                                'font-bold' => 'Bold',
                            ]),
                    ]),

                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\ColorPicker::make('recommended_services_item_bg_color')
                            ->label('Item Background Color'),
                        Forms\Components\ColorPicker::make('recommended_services_item_text_color')
                            ->label('Item Text Color'),
                        Forms\Components\ColorPicker::make('recommended_services_item_desc_color')
                            ->label('Item Description Color'),
                    ]),

                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\ColorPicker::make('recommended_services_item_icon_color')
                            ->label('Item Icon Color'),
                        Forms\Components\TextInput::make('recommended_services_item_icon_size')
                            ->label('Item Icon Size (e.g., 4, 5, 6 for w-X h-X)'),
                    ]),

                    Forms\Components\Repeater::make('recommended_services_items')
                        ->label('Services')
                        ->schema([
                            Forms\Components\TextInput::make('title')
                                ->label('Service Name')
                                ->required(),
                            Forms\Components\TextInput::make('description')
                                ->label('Description')
                                ->required(),
                            Forms\Components\TextInput::make('icon')
                                ->label('Lucide Icon (Optional)')
                                ->default('arrow-right'),
                            Forms\Components\TextInput::make('link')
                                ->label('Link URL (Optional)'),
                        ])
                        ->grid(2)
                        ->columnSpanFull(),
                ])->columns(2),
            ]);
    }
}
