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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();

            $table->foreignId('payment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('recipient_id')->constrained('users')->cascadeOnDelete();

            $table->string('kind', 30); // brand_charge | influencer_payout
            $table->bigInteger('amount_cents');
            $table->string('currency', 3)->default('USD');

            $table->timestamp('issued_at');
            $table->timestamp('paid_at')->nullable();

            $table->jsonb('snapshot')->default('{}'); // brand/influencer/campaign details at time of issue

            $table->timestamps();

            $table->index('recipient_id');
            $table->index('kind');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
