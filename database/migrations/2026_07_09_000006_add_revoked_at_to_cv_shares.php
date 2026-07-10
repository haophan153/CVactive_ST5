<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * SECURITY (fix #13): revoked_at allows users to instantly kill a share link
 * without waiting for expires_at.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cv_shares', function (Blueprint $table) {
            $table->timestamp('revoked_at')->nullable()->after('expires_at');
            $table->index('revoked_at');
        });
    }

    public function down(): void
    {
        Schema::table('cv_shares', function (Blueprint $table) {
            $table->dropIndex(['revoked_at']);
            $table->dropColumn('revoked_at');
        });
    }
};
