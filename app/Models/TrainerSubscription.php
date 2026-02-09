<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainerSubscription extends Model
{
    protected $table = 'trainer_subscriptions';

    protected $fillable = [
        'user_id',
        'plan_name',
        'status',
        'renewal_date',
    ];

    protected function casts(): array
    {
        return [
            'renewal_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function firstOrCreateForUser(User $user): self
    {
        return self::firstOrCreate(
            ['user_id' => $user->id],
            [
                'plan_name' => 'Free',
                'status' => 'active',
                'renewal_date' => now()->addMonth(),
            ]
        );
    }
}
