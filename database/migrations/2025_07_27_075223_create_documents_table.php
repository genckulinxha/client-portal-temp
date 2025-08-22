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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('filename');
            $table->string('original_filename');
            $table->string('file_path');
            $table->string('mime_type');
            $table->bigInteger('file_size');
            $table->string('file_hash')->nullable(); // For duplicate detection
            
            // Document categorization
            $table->enum('type', [
                'client_document', 'pleading', 'discovery', 'correspondence', 
                'court_filing', 'settlement_doc', 'retainer', 'intake_form', 'other'
            ])->default('client_document');
            
            // Access control
            $table->boolean('client_viewable')->default(false);
            $table->boolean('is_confidential')->default(false);
            
            // Relationships
            $table->foreignId('case_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('client_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('task_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('uploaded_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('uploaded_by_client_id')->nullable()->constrained('clients')->onDelete('set null');
            
            // Version control
            $table->integer('version')->default(1);
            $table->foreignId('parent_document_id')->nullable()->constrained('documents')->onDelete('cascade');
            
            $table->text('description')->nullable();
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
        Schema::dropIfExists('documents');
    }
};
