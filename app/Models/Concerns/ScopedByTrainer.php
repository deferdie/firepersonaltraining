<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait ScopedByTrainer
{
    /**
     * Scope the query to the given trainer (or the authenticated user when null).
     */
    public function scopeForTrainer(Builder $query, ?int $trainerId = null): Builder
    {
        return $query->where('trainer_id', $trainerId ?? auth()->id());
    }
}
