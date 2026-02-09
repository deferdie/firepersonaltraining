<?php

namespace App\Models;

use App\Models\Concerns\ScopedByTrainer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LibraryDocument extends Model
{
    use HasFactory, ScopedByTrainer;

    protected $table = 'library_documents';

    protected $fillable = [
        'trainer_id',
        'title',
        'description',
        'file_url',
        'file_type',
        'file_size',
        'category',
    ];

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }
}
