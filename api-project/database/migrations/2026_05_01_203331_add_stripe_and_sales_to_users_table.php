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
        Schema::table('users', function (Blueprint $table) {
            $table->string('stripe_account_id')->nullable()->after('country_id');
            $table->boolean('stripe_onboarded')->default(false)->after('stripe_account_id');

            $table->foreignId('sales_rep_id')->nullable()->after('stripe_onboarded')->constrained('users')->nullOnDelete();
            $table->bigInteger('monthly_budget_cents')->nullable()->after('sales_rep_id');
            $table->bigInteger('income_target_cents')->nullable()->after('monthly_budget_cents');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['sales_rep_id']);
            $table->dropColumn([
                'stripe_account_id',
                'stripe_onboarded',
                'sales_rep_id',
                'monthly_budget_cents',
                'income_target_cents',
            ]);
        });
    }
};
