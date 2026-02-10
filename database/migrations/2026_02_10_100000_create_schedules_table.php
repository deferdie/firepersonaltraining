<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainer_id')->constrained('users')->onDelete('cascade');
            $table->morphs('assignable');
            $table->morphs('schedulable');
            $table->string('title');
            $table->text('notes')->nullable();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at')->nullable();
            $table->string('timezone', 64)->default('UTC');
            $table->json('recurrence_rule')->nullable();
            $table->date('recurrence_ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('starts_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
