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
        Schema::create('portfolios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();

            $table->text('bio')->nullable();
            $table->jsonb('content_style')->default('[]'); // tags: ['lifestyle', 'beauty', 'sports']
            $table->unsignedBigInteger('audience_size')->nullable();
            $table->jsonb('audience_demographics')->default('{}'); // age_buckets, regions, gender_split
            $table->jsonb('past_collabs')->default('[]'); // [{brand, year, deliverables, link}]

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portfolios');
    }
};
