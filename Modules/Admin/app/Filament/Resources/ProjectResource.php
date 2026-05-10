<?php

namespace Modules\Admin\Filament\Resources;

use Modules\Admin\Filament\Resources\ProjectResource\Pages;
use Modules\Admin\Filament\Resources\ProjectResource\RelationManagers;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationGroup = 'Operations';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Project Management')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('General Overview')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\Section::make('Core Participants')
                                            ->description('Identification of the main parties involved in the project.')
                                            ->schema([
                                                Forms\Components\Select::make('client_id')
                                                    ->label('Client (Account Owner)')
                                                    ->relationship('client', 'name', fn (Builder $query) => $query->where('role', 'client'))
                                                    ->required()
                                                    ->searchable()
                                                    ->preload(),
                                                Forms\Components\Select::make('company_id')
                                                    ->label('Client Company')
                                                    ->relationship('company', 'name')
                                                    ->searchable()
                                                    ->preload(),
                                                Forms\Components\Select::make('provider_id')
                                                    ->label('Service Provider (Lead)')
                                                    ->relationship('provider', 'name', fn (Builder $query) => $query->where('role', 'provider'))
                                                    ->required()
                                                    ->searchable()
                                                    ->preload(),
                                            ])->columnSpan(2),
                                        
                                        Forms\Components\Section::make('Project Status')
                                            ->description('Current operational state of the project.')
                                            ->schema([
                                                Forms\Components\Select::make('status')
                                                    ->options([
                                                        'pending' => 'Pending Initialization',
                                                        'active' => 'Active / In-Progress',
                                                        'completed' => 'Successfully Completed',
                                                        'disputed' => 'Under Dispute',
                                                        'cancelled' => 'Terminated / Cancelled',
                                                    ])
                                                    ->required()
                                                    ->native(false),
                                                Forms\Components\Toggle::make('provider_marked_complete')
                                                    ->label('Provider Flagged as Complete')
                                                    ->disabled(),
                                                Forms\Components\Toggle::make('client_approved')
                                                    ->label('Client Final Approval')
                                                    ->disabled(),
                                            ])->columnSpan(1),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Standardized Service')
                            ->icon('heroicon-o-briefcase')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Section::make('Service Definition')
                                            ->schema([
                                                Forms\Components\Select::make('service_id')
                                                    ->label('Catalog Service')
                                                    ->relationship('service', 'name')
                                                    ->required()
                                                    ->searchable()
                                                    ->preload(),
                                                Forms\Components\Select::make('provider_service_id')
                                                    ->label('Provider Offering Details')
                                                    ->relationship('providerService', 'id', function (Builder $query, Forms\Get $get) {
                                                        if ($get('provider_id')) {
                                                            $query->where('provider_id', $get('provider_id'));
                                                        }
                                                        return $query;
                                                    })
                                                    ->getOptionLabelFromRecordUsing(fn ($record) => "SAR {$record->price} - {$record->delivery_time_days} Days")
                                                    ->searchable()
                                                    ->preload(),
                                            ]),
                                        Forms\Components\Section::make('Scope Summary')
                                            ->schema([
                                                Forms\Components\Placeholder::make('service_category')
                                                    ->label('Category')
                                                    ->content(fn ($record) => $record?->service?->serviceCategory?->name ?? 'N/A'),
                                                Forms\Components\Placeholder::make('standard_subtasks')
                                                    ->label('Standardized Subtasks')
                                                    ->content(fn ($record) => collect($record?->service?->subtasks)->implode(', ') ?: 'No subtasks defined'),
                                            ]),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Financial Strategy')
                            ->icon('heroicon-o-currency-dollar')
                            ->schema([
                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\Section::make('Budgeting')
                                            ->schema([
                                                Forms\Components\TextInput::make('total_amount')
                                                    ->label('Agreed Contract Value')
                                                    ->required()
                                                    ->numeric()
                                                    ->prefix('SAR'),
                                                Forms\Components\DateTimePicker::make('escrow_released_at')
                                                    ->label('Funds Release Timestamp')
                                                    ->disabled(),
                                            ])->columnSpan(1),
                                        
                                        Forms\Components\Section::make('Timeline')
                                            ->schema([
                                                Forms\Components\DateTimePicker::make('start_date')
                                                    ->label('Official Commencement'),
                                                Forms\Components\DateTimePicker::make('end_date')
                                                    ->label('Projected Delivery'),
                                                Forms\Components\DateTimePicker::make('completed_at')
                                                    ->label('Actual Completion Date')
                                                    ->disabled(),
                                            ])->columnSpan(2),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Incident Management')
                            ->icon('heroicon-o-exclamation-circle')
                            ->schema([
                                Forms\Components\Section::make('Exceptions & Terminations')
                                    ->schema([
                                        Forms\Components\Textarea::make('dispute_reason')
                                            ->label('Formal Dispute Justification')
                                            ->columnSpanFull(),
                                        Forms\Components\Textarea::make('termination_reason')
                                            ->label('Contract Termination Reason')
                                            ->columnSpanFull(),
                                        Forms\Components\Textarea::make('rejection_reason')
                                            ->label('Service Rejection Feedback')
                                            ->columnSpanFull(),
                                    ])->collapsible(),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company.name')
                    ->label('Client Company')
                    ->description(fn (Project $record) => $record->client->name)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('provider.providerProfile.company_name')
                    ->label('Service Provider')
                    ->description(fn (Project $record) => $record->provider->name)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('service.name')
                    ->label('Service Details')
                    ->description(fn (Project $record) => $record->service?->serviceCategory?->name)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'active' => 'info',
                        'completed' => 'success',
                        'disputed' => 'danger',
                        'cancelled' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => strtoupper($state)),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Budget')
                    ->money('SAR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Due Date')
                    ->dateTime('M d, Y')
                    ->color(fn ($state) => now()->gt($state) ? 'danger' : 'gray')
                    ->sortable(),
                Tables\Columns\TextColumn::make('progress')
                    ->label('Task Progress')
                    ->getStateUsing(function (Project $record) {
                        $total = $record->tasks()->count();
                        if ($total === 0) return '0%';
                        $completed = $record->tasks()->where('status', 'completed')->count();
                        $percentage = round(($completed / $total) * 100);
                        return "{$percentage}%";
                    })
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        $state === '100%' => 'success',
                        (int)$state > 50 => 'info',
                        default => 'warning',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'active' => 'Active',
                        'completed' => 'Completed',
                        'disputed' => 'Disputed',
                        'cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\SelectFilter::make('client_id')
                    ->label('Client')
                    ->relationship('client', 'name', fn ($query) => $query->where('role', 'client'))
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('provider_id')
                    ->label('Provider')
                    ->relationship('provider', 'name', fn ($query) => $query->where('role', 'provider'))
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('category')
                    ->label('Service Category')
                    ->relationship('service.serviceCategory', 'name'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            RelationManagers\TasksRelationManager::class,
            RelationManagers\PaymentsRelationManager::class,
            RelationManagers\ReleaseRequestsRelationManager::class,
            RelationManagers\ReviewsRelationManager::class,
            RelationManagers\ProjectHistoriesRelationManager::class,
            RelationManagers\DocumentsRelationManager::class,
            RelationManagers\MessagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
