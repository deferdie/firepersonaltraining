<?php

namespace App\Models;

use App\Models\Concerns\ScopedByTrainer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LibraryMealPlan extends Model
{
    use HasFactory, ScopedByTrainer;

    protected $table = 'library_meal_plans';

    protected $fillable = [
        'trainer_id',
        'name',
        'description',
        'weeks',
        'meals',
        'calories_target',
    ];

    protected $casts = [
        'meals' => 'array',
    ];

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }
}
