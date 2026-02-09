<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_feeds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->string('activity_type'); // e.g., 'trainer_note', 'progress_photo', 'workout_completion'
            $table->unsignedBigInteger('activity_id'); // ID of the related record
            $table->string('performed_by_type'); // Polymorphic: 'App\Models\User'
            $table->unsignedBigInteger('performed_by_id'); // ID of who performed the activity
            $table->json('metadata')->nullable(); // Flexible storage for activity-specific data
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['client_id', 'created_at']);
            $table->index('activity_type');
            $table->index(['activity_type', 'activity_id']);
            $table->index(['performed_by_type', 'performed_by_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_feeds');
    }
};
