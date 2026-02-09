<?php

namespace App\Models;

use App\Models\Concerns\ScopedByTrainer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LibraryExercise extends Model
{
    use HasFactory, ScopedByTrainer;

    protected $table = 'library_exercises';

    protected $fillable = [
        'trainer_id',
        'name',
        'description',
        'muscle_group',
        'difficulty',
        'equipment',
        'video_url',
        'thumbnail_url',
        'instructions',
    ];

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }
}
