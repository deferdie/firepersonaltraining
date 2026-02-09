<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('progress_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->string('photo_url');
            $table->string('angle')->nullable();
            $table->decimal('weight', 5, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('taken_at')->useCurrent();
            $table->timestamps();
            
            $table->index(['client_id', 'taken_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('progress_photos');
    }
};
