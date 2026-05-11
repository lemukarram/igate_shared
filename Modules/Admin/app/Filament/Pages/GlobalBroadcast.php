<?php

namespace Modules\Admin\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class GlobalBroadcast extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationGroup = 'System Settings';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'modules.admin.filament.pages.global-broadcast';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('segment')
                    ->label('Target Audience')
                    ->options([
                        'all' => 'All Users',
                        'clients' => 'All Main Clients',
                        'providers' => 'All Main Providers',
                        'client_staff' => 'All Client Staff',
                        'provider_staff' => 'All Provider Staff',
                    ])
                    ->required(),
                TextInput::make('subject')
                    ->label('Subject / Title')
                    ->required(),
                RichEditor::make('message')
                    ->label('Message Body')
                    ->required(),
            ])
            ->statePath('data');
    }

    public function getFormActions(): array
    {
        return [
            Action::make('send')
                ->label('Send Broadcast')
                ->submit('sendBroadcast')
                ->color('primary')
                ->icon('heroicon-o-paper-airplane'),
        ];
    }

    public function sendBroadcast(): void
    {
        $data = $this->form->getState();

        // Logic to dispatch notifications or emails to the segmented users would go here.
        // For now, we simulate the action.

        Notification::make()
            ->title('Broadcast Sent Successfully')
            ->body('The message has been queued for delivery to ' . str_replace('_', ' ', $data['segment']) . '.')
            ->success()
            ->send();

        $this->form->fill();
    }
}
