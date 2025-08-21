// database/migrations/2024_01_02_000005_create_jobs_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jobz', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->string('slug');
            $table->text('description');
            $table->text('requirements')->nullable();
            $table->text('benefits')->nullable();
            $table->string('location');
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->default('Uganda');
            $table->enum('type', ['full-time', 'part-time', 'contract', 'temporary', 'internship', 'remote'])->default('full-time');
            $table->enum('experience_level', ['entry', 'mid', 'senior', 'executive'])->default('mid');
            $table->decimal('salary_min', 10, 2)->nullable();
            $table->decimal('salary_max', 10, 2)->nullable();
            $table->enum('salary_period', ['hourly', 'monthly', 'yearly'])->default('monthly');
            $table->boolean('salary_negotiable')->default(false);
            $table->enum('status', ['draft', 'active', 'paused', 'closed', 'expired'])->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_urgent')->default(false);
            $table->boolean('remote_allowed')->default(false);
            $table->json('required_skills')->nullable(); // Array of required skills
            $table->json('preferred_skills')->nullable(); // Array of preferred skills
            $table->string('application_email')->nullable();
            $table->string('application_url')->nullable();
            $table->text('application_instructions')->nullable();
            $table->integer('positions_available')->default(1);
            $table->integer('views_count')->default(0);
            $table->integer('applications_count')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'status']);
            $table->index(['category_id', 'status', 'published_at']);
            $table->index(['status', 'is_featured', 'published_at']);
            $table->index(['location', 'status', 'published_at']);
            $table->index(['type', 'status', 'published_at']);
            $table->index(['expires_at', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};