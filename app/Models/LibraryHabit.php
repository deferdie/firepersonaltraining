<?php

namespace App\Models;

use App\Models\Concerns\ScopedByTrainer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LibraryHabit extends Model
{
    use HasFactory, ScopedByTrainer;

    protected $table = 'library_habits';

    protected $fillable = [
        'trainer_id',
        'name',
        'description',
    ];

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }
}
