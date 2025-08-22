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
        Schema::create('time_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('case_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('task_id')->nullable()->constrained()->onDelete('set null');
            
            $table->decimal('hours', 5, 1); // 0.1 hour increments
            $table->text('description');
            $table->date('date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            
            $table->boolean('billable')->default(true);
            $table->decimal('hourly_rate', 8, 2)->nullable();
            $table->decimal('total_amount', 10, 2)->nullable();
            
            $table->enum('status', ['draft', 'submitted', 'approved', 'billed'])->default('draft');
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
        Schema::dropIfExists('time_entries');
    }
};
