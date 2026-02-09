<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trainer_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->string('category')->nullable();
            $table->text('content');
            $table->timestamps();
            
            $table->index(['trainer_id', 'client_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trainer_notes');
    }
};
