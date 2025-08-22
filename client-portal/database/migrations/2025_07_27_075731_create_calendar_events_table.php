<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('calendar_events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            
            $table->datetime('start_datetime');
            $table->datetime('end_datetime');
            $table->boolean('all_day')->default(false);
            
            // Google Calendar integration
            $table->string('google_event_id')->nullable()->unique();
            $table->boolean('synced_with_google')->default(false);
            $table->datetime('last_google_sync')->nullable();
            
            // Relationships
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('case_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('client_id')->nullable()->constrained()->onDelete('cascade');
            
            // Event type and location
            $table->enum('type', ['meeting', 'court_hearing', 'deposition', 'consultation', 'deadline', 'other'])->default('meeting');
            $table->string('location')->nullable();
            $table->string('meeting_link')->nullable(); // Zoom, Teams, etc.
            
            // Attendees (JSON array of email addresses)
            $table->json('attendees')->nullable();
            
            // Reminders
            $table->json('reminders')->nullable(); // Array of reminder times
            
            $table->enum('status', ['scheduled', 'completed', 'cancelled', 'rescheduled'])->default('scheduled');
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendar_events');
    }
};
