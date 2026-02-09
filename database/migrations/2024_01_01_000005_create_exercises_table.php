<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_id')->constrained('workouts')->onDelete('cascade');
            $table->string('name');
            $table->integer('sets')->nullable();
            $table->string('reps')->nullable();
            $table->integer('rest_seconds')->nullable();
            $table->text('notes')->nullable();
            $table->integer('order_index')->default(0);
            $table->timestamps();
            
            $table->index(['workout_id', 'order_index']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exercises');
    }
};
