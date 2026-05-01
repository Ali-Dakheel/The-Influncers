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
        Schema::table('campaigns', function (Blueprint $table) {
            $table->boolean('is_urgent')->default(false)->after('completed_at');
            $table->timestamp('urgent_expires_at')->nullable()->after('is_urgent');
            $table->timestamp('archived_at')->nullable()->after('urgent_expires_at');

            $table->index('is_urgent');
            $table->index('archived_at');
        });
    }

    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropIndex(['is_urgent']);
            $table->dropIndex(['archived_at']);
            $table->dropColumn(['is_urgent', 'urgent_expires_at', 'archived_at']);
        });
    }
};
