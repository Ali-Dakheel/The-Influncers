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
        Schema::create('outcomes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->foreignId('influencer_id')->constrained('users')->cascadeOnDelete();

            // Snapshot of typed flywheel columns from the campaign at the moment of completion
            $table->string('platform', 30);
            $table->string('category', 30);
            $table->foreignId('country_id')->nullable()->constrained()->nullOnDelete();
            $table->string('format', 30);
            $table->string('objective', 30);

            // The post + actual results (nullable until brand completes campaign)
            $table->string('final_post_url')->nullable();
            $table->unsignedBigInteger('reach')->nullable();
            $table->unsignedBigInteger('engagement')->nullable();
            $table->unsignedBigInteger('conversions')->nullable();
            $table->unsignedBigInteger('cost_per_result_cents')->nullable();
            $table->bigInteger('paid_price_cents')->nullable();

            $table->timestamp('recorded_at')->nullable();

            $table->timestamps();

            $table->unique(['campaign_id', 'application_id']);
            $table->index('campaign_id');
            $table->index('influencer_id');
            $table->index('category');
            $table->index('country_id');
            $table->index('platform');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outcomes');
    }
};
