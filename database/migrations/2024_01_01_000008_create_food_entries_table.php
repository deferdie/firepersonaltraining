<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('food_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->string('meal_type')->nullable();
            $table->string('food_name');
            $table->integer('calories')->nullable();
            $table->decimal('protein', 8, 2)->nullable();
            $table->decimal('carbs', 8, 2)->nullable();
            $table->decimal('fat', 8, 2)->nullable();
            $table->timestamp('logged_at')->useCurrent();
            $table->timestamps();
            
            $table->index(['client_id', 'logged_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_entries');
    }
};
