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
        Schema::create('cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('attorney_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('paralegal_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('case_number')->unique();
            $table->string('case_title');
            $table->enum('case_type', ['fcra_dispute', 'fcra_lawsuit', 'identity_theft', 'other'])->default('fcra_dispute');
            $table->enum('status', ['intake', 'investigation', 'litigation', 'settlement', 'closed'])->default('intake');
            $table->text('description')->nullable();
            $table->decimal('potential_damages', 10, 2)->nullable();
            $table->decimal('settlement_amount', 10, 2)->nullable();
            $table->date('statute_limitations')->nullable();
            $table->date('filed_date')->nullable();
            $table->date('closed_date')->nullable();
            $table->json('metadata')->nullable(); // Flexible case-specific data
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cases');
    }
};
