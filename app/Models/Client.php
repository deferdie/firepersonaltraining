<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'trainer_id',
        'user_id',
        'name',
        'email',
        'phone',
        'status',
    ];

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function programs(): HasMany
    {
        return $this->hasMany(ClientProgram::class);
    }

    public function workoutCompletions(): HasMany
    {
        return $this->hasMany(WorkoutCompletion::class);
    }

    public function foodEntries(): HasMany
    {
        return $this->hasMany(FoodEntry::class);
    }

    public function progressPhotos(): HasMany
    {
        return $this->hasMany(ProgressPhoto::class);
    }

    public function activityFeeds(): HasMany
    {
        return $this->hasMany(ActivityFeed::class);
    }
}
