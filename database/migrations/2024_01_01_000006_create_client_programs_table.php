<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('program_id')->constrained('programs')->onDelete('cascade');
            $table->date('start_date')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
            
            $table->unique(['client_id', 'program_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_programs');
    }
};
