<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Habit extends Model
{
    use HasFactory;

    protected $fillable = [
        'trainer_id',
        'assignable_type',
        'assignable_id',
        'name',
        'description',
        'source_library_habit_id',
    ];

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function assignable(): MorphTo
    {
        return $this->morphTo();
    }

    public function sourceLibraryHabit(): BelongsTo
    {
        return $this->belongsTo(LibraryHabit::class, 'source_library_habit_id');
    }
}
