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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['client_task', 'internal_task'])->default('internal_task');
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            
            // Assignable to users or clients
            $table->foreignId('assigned_to_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('assigned_to_client_id')->nullable()->constrained('clients')->onDelete('cascade');
            
            // Associated with case
            $table->foreignId('case_id')->nullable()->constrained()->onDelete('cascade');
            
            // Created by user
            $table->foreignId('created_by_user_id')->constrained('users')->onDelete('restrict');
            
            $table->datetime('due_date')->nullable();
            $table->datetime('completed_at')->nullable();
            $table->json('requirements')->nullable(); // For client tasks (document upload, form completion, etc.)
            $table->text('completion_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
