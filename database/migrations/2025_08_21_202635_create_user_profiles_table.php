// database/migrations/2024_01_02_000004_create_user_profiles_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->default('Uganda');
            $table->string('postal_code')->nullable();
            $table->text('bio')->nullable();
            $table->json('skills')->nullable(); // Array of skills
            $table->integer('experience_years')->default(0);
            $table->string('resume_path')->nullable();
            $table->string('portfolio_url')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('github_url')->nullable();
            $table->string('website_url')->nullable();
            $table->enum('availability', ['available', 'employed', 'not_looking'])->default('available');
            $table->decimal('expected_salary_min', 10, 2)->nullable();
            $table->decimal('expected_salary_max', 10, 2)->nullable();
            $table->string('preferred_location')->nullable();
            $table->boolean('open_to_remote')->default(true);
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other', 'prefer_not_to_say'])->nullable();
            $table->timestamps();

            $table->index(['user_id', 'availability']);
            $table->index(['city', 'availability']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};