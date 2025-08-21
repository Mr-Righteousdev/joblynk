// database/migrations/2024_01_02_000010_create_job_alerts_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Alert name
            $table->string('keywords')->nullable();
            $table->string('location')->nullable();
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->json('job_types')->nullable(); // Array of job types
            $table->decimal('salary_min', 10, 2)->nullable();
            $table->enum('frequency', ['immediate', 'daily', 'weekly'])->default('daily');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_sent_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'is_active']);
            $table->index(['frequency', 'is_active', 'last_sent_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_alerts');
    }
};