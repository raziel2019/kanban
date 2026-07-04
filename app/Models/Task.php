<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['title', 'description', 'status', 'position', 'priority', 'due_date'])]
class Task extends Model
{
    public const STATUSES = [
        'todo' => 'Por hacer',
        'in_progress' => 'En progreso',
        'review' => 'En revision',
        'completed' => 'Completado',
    ];

    public const PRIORITIES = [
        'low' => 'Baja',
        'medium' => 'Media',
        'high' => 'Alta',
        'urgent' => 'Urgente',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'position' => 'decimal:10',
        ];
    }
}
