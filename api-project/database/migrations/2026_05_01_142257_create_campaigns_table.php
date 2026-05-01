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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();

            // Ownership
            $table->foreignId('brand_id')->constrained('users')->cascadeOnDelete();

            // Brief content
            $table->string('title');
            $table->text('description');
            $table->text('deliverables')->nullable();

            // Mood board (lightweight; richer model in Phase 2B)
            $table->string('mood_board_title')->nullable();
            $table->text('mood_board_description')->nullable();

            // Flywheel columns (typed from day one per design spec §5)
            $table->string('category', 30);
            $table->foreignId('country_id')->nullable()->constrained()->nullOnDelete();
            $table->jsonb('platforms');
            $table->string('format', 30);
            $table->string('objective', 30);
            $table->bigInteger('budget_cents');
            $table->string('currency', 3)->default('USD');

            // State machine
            $table->string('state', 20)->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('paused_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            // Scheduling
            $table->date('starts_on')->nullable();
            $table->date('ends_on')->nullable();
            $table->timestamp('application_deadline')->nullable();

            $table->timestamps();

            $table->index('brand_id');
            $table->index('state');
            $table->index('category');
            $table->index('country_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
