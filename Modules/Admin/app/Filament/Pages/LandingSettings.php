<?php

namespace Modules\Admin\Filament\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use App\Settings\LandingPageSettings;

class LandingSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';
    protected static ?string $navigationGroup = 'Website Management';
    protected static ?string $navigationLabel = 'Landing Page';
    protected static ?int $navigationSort = 3;
    protected static string $settings = LandingPageSettings::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Landing Page Sections')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Hero Section')
                            ->schema([
                                Forms\Components\Section::make('Page & Site Title')
                                    ->schema([
                                        Forms\Components\TextInput::make('site_title_en')->label('Browser Tab Title (EN)')->required(),
                                        Forms\Components\TextInput::make('site_title_ar')->label('Browser Tab Title (AR)')->required(),
                                    ])->columns(2),
                                Forms\Components\Section::make('Badge & Titles')
                                    ->schema([
                                        Forms\Components\TextInput::make('hero_badge_en')->label('Badge Text (EN)')->required(),
                                        Forms\Components\TextInput::make('hero_badge_ar')->label('Badge Text (AR)')->required(),
                                        Forms\Components\TextInput::make('hero_title_en')->label('Main Title (EN)')->required(),
                                        Forms\Components\TextInput::make('hero_title_ar')->label('Main Title (AR)')->required(),
                                        Forms\Components\Textarea::make('hero_subtitle_en')->label('Subtitle (EN)')->required(),
                                        Forms\Components\Textarea::make('hero_subtitle_ar')->label('Subtitle (AR)')->required(),
                                    ])->columns(2),
                                Forms\Components\Section::make('Call to Action Buttons')
                                    ->schema([
                                        Forms\Components\TextInput::make('hero_cta_client_en')->label('Client Button Text (EN)')->required(),
                                        Forms\Components\TextInput::make('hero_cta_client_ar')->label('Client Button Text (AR)')->required(),
                                        Forms\Components\TextInput::make('hero_cta_provider_en')->label('Provider Button Text (EN)')->required(),
                                        Forms\Components\TextInput::make('hero_cta_provider_ar')->label('Provider Button Text (AR)')->required(),
                                    ])->columns(2),
                            ]),

                        Forms\Components\Tabs\Tab::make('Why iGate Section')
                            ->schema([
                                Forms\Components\Section::make('Section Header')
                                    ->schema([
                                        Forms\Components\TextInput::make('why_title_en')->label('Title (EN)')->required(),
                                        Forms\Components\TextInput::make('why_title_ar')->label('Title (AR)')->required(),
                                        Forms\Components\Textarea::make('why_subtitle_en')->label('Subtitle (EN)')->required(),
                                        Forms\Components\Textarea::make('why_subtitle_ar')->label('Subtitle (AR)')->required(),
                                    ])->columns(2),
                                Forms\Components\Repeater::make('why_features')
                                    ->label('Features')
                                    ->schema([
                                        Forms\Components\TextInput::make('icon')->label('Lucide Icon Name')->required(),
                                        Forms\Components\TextInput::make('title_en')->label('Title (EN)')->required(),
                                        Forms\Components\TextInput::make('title_ar')->label('Title (AR)')->required(),
                                        Forms\Components\Textarea::make('desc_en')->label('Description (EN)')->required(),
                                        Forms\Components\Textarea::make('desc_ar')->label('Description (AR)')->required(),
                                    ])
                                    ->grid(2)
                                    ->columnSpanFull(),
                            ]),

                        Forms\Components\Tabs\Tab::make('Services & Pricing Titles')
                            ->schema([
                                Forms\Components\Section::make('Services Section Header')
                                    ->schema([
                                        Forms\Components\TextInput::make('services_title_en')->label('Title (EN)')->required(),
                                        Forms\Components\TextInput::make('services_title_ar')->label('Title (AR)')->required(),
                                        Forms\Components\Textarea::make('services_subtitle_en')->label('Subtitle (EN)')->required(),
                                        Forms\Components\Textarea::make('services_subtitle_ar')->label('Subtitle (AR)')->required(),
                                    ])->columns(2),
                                Forms\Components\Section::make('Pricing Section Header')
                                    ->schema([
                                        Forms\Components\TextInput::make('pricing_title_en')->label('Title (EN)')->required(),
                                        Forms\Components\TextInput::make('pricing_title_ar')->label('Title (AR)')->required(),
                                        Forms\Components\Textarea::make('pricing_subtitle_en')->label('Subtitle (EN)')->required(),
                                        Forms\Components\Textarea::make('pricing_subtitle_ar')->label('Subtitle (AR)')->required(),
                                    ])->columns(2),
                            ]),

                        Forms\Components\Tabs\Tab::make('Footer')
                            ->schema([
                                Forms\Components\Section::make('Footer Content')
                                    ->schema([
                                        Forms\Components\Textarea::make('footer_description_en')->label('Description (EN)')->required(),
                                        Forms\Components\Textarea::make('footer_description_ar')->label('Description (AR)')->required(),
                                    ])->columns(2),
                                Forms\Components\Section::make('Social Media Links')
                                    ->schema([
                                        Forms\Components\TextInput::make('twitter_url')->label('Twitter URL')->url(),
                                        Forms\Components\TextInput::make('linkedin_url')->label('LinkedIn URL')->url(),
                                    ])->columns(2),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }
}
