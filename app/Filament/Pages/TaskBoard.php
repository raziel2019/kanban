<?php

namespace App\Filament\Pages;

use App\Models\Task;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Relaticle\Flowforge\Board;
use Relaticle\Flowforge\BoardPage;
use Relaticle\Flowforge\Column;
use Relaticle\Flowforge\Components\CardFlex;
use Relaticle\Flowforge\Services\DecimalPosition;

class TaskBoard extends BoardPage
{
    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-view-columns';
    protected static ?string $navigationLabel = 'Kanban';
    protected static ?string $title = 'Kanban de tareas';

    public function board(Board $board): Board
    {
        return $board
            ->query($this->getEloquentQuery())
            ->recordTitleAttribute('title')
            ->columnIdentifier('status')
            ->positionIdentifier('position')
            ->cardsPerColumn(30)
            ->cardSchema(fn (Schema $schema): Schema => $schema->components([
                TextEntry::make('title')
                    ->hiddenLabel()
                    ->weight('bold'),
                TextEntry::make('description')
                    ->hiddenLabel()
                    ->placeholder('Sin descripcion')
                    ->lineClamp(2),
                CardFlex::make([
                    TextEntry::make('priority')
                        ->hiddenLabel()
                        ->badge()
                        ->formatStateUsing(fn (string $state): string => Task::PRIORITIES[$state] ?? $state)
                        ->color(fn (string $state): string => match ($state) {
                            'urgent' => 'danger',
                            'high' => 'warning',
                            'medium' => 'info',
                            default => 'gray',
                        }),
                    TextEntry::make('due_date')
                        ->hiddenLabel()
                        ->date('d M Y')
                        ->placeholder('Sin fecha')
                        ->icon('heroicon-o-calendar-days'),
                ]),
            ]))
            ->columns([
                Column::make('todo')->label('Por hacer')->color('gray'),
                Column::make('in_progress')->label('En progreso')->color('blue'),
                Column::make('review')->label('Revision')->color('warning'),
                Column::make('completed')->label('Completado')->color('success'),
            ]);
    }

    public function getEloquentQuery(): Builder
    {
        return Task::query();
    }

    /**
     * @return array<Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('createTask')
                ->label('Nueva tarea')
                ->icon('heroicon-o-plus')
                ->schema([
                    TextInput::make('title')
                        ->label('Titulo')
                        ->required()
                        ->maxLength(255),
                    Textarea::make('description')
                        ->label('Descripcion')
                        ->rows(3),
                    Grid::make(3)
                        ->schema([
                            Select::make('status')
                                ->label('Estado')
                                ->options(Task::STATUSES)
                                ->default('todo')
                                ->required(),
                            Select::make('priority')
                                ->label('Prioridad')
                                ->options(Task::PRIORITIES)
                                ->default('medium')
                                ->required(),
                            DatePicker::make('due_date')
                                ->label('Fecha limite'),
                        ]),
                ])
                ->action(function (array $data): void {
                    $status = $data['status'] ?? 'todo';
                    $lastPosition = Task::query()
                        ->where('status', $status)
                        ->max('position');

                    Task::query()->create([
                        ...$data,
                        'position' => $lastPosition
                            ? DecimalPosition::after((string) $lastPosition)
                            : DecimalPosition::forEmptyColumn(),
                    ]);
                }),
        ];
    }
}
