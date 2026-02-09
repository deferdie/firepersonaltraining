<?php

namespace App\Models;

use App\Models\Concerns\ScopedByTrainer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LibraryForm extends Model
{
    use HasFactory, ScopedByTrainer;

    protected $table = 'library_forms';

    protected $fillable = [
        'trainer_id',
        'name',
        'description',
        'form_type',
        'fields',
    ];

    protected $casts = [
        'fields' => 'array',
    ];

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }
}
