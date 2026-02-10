<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'trainer_id',
        'assignable_type',
        'assignable_id',
        'schedulable_type',
        'schedulable_id',
        'title',
        'notes',
        'starts_at',
        'ends_at',
        'timezone',
        'recurrence_rule',
        'recurrence_ends_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'recurrence_ends_at' => 'date',
            'recurrence_rule' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function assignable(): MorphTo
    {
        return $this->morphTo();
    }

    public function schedulable(): MorphTo
    {
        return $this->morphTo();
    }
}
