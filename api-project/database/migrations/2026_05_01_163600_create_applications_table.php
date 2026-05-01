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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();

            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->foreignId('influencer_id')->constrained('users')->cascadeOnDelete();

            $table->string('status', 20)->default('pending');
            $table->text('pitch');
            $table->bigInteger('proposed_price_cents')->nullable();
            $table->string('currency', 3)->default('USD');

            $table->timestamp('applied_at');
            $table->timestamp('decided_at')->nullable();
            $table->foreignId('decided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('decision_note')->nullable();

            $table->timestamps();

            $table->unique(['campaign_id', 'influencer_id']);
            $table->index('status');
            $table->index('campaign_id');
            $table->index('influencer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
