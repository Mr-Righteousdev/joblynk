// database/migrations/2024_01_02_000006_create_applications_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('cover_letter')->nullable();
            $table->string('resume_path')->nullable(); // Specific resume for this application
            $table->json('answers')->nullable(); // Answers to custom application questions
            $table->enum('status', ['pending', 'reviewed', 'shortlisted', 'interview', 'offered', 'hired', 'rejected'])->default('pending');
            $table->text('employer_notes')->nullable();
            $table->decimal('offered_salary', 10, 2)->nullable();
            $table->timestamp('applied_at')->useCurrent();
            $table->timestamp('viewed_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamp('interview_scheduled_at')->nullable();
            $table->string('interview_location')->nullable();
            $table->text('interview_notes')->nullable();
            $table->timestamps();

            $table->unique(['job_id', 'user_id']); // Prevent duplicate applications
            $table->index(['user_id', 'status', 'applied_at']);
            $table->index(['job_id', 'status', 'applied_at']);
            $table->index(['status', 'applied_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};