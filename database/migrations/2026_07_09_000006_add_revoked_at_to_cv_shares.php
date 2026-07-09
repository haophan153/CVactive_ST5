<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * M-4 + L-11: revoked_at column cho phép user audit
 * share token lifecycle (khi nào revoke, có thể trong tương lai
 * show lịch sử share access nếu cần).
 */
return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('cv_shares', 'revoked_at')) {
            Schema::table('cv_shares', function (Blueprint $table) {
                $table->timestamp('revoked_at')->nullable()->after('expires_at');
                $table->timestamp('last_viewed_at')->nullable()->after('revoked_at');
                $table->string('revoke_reason', 255)->nullable()->after('last_viewed_at');
            });
        }
    }

    public function down(): void
    {
        Schema::table('cv_shares', function (Blueprint $table) {
            $table->dropColumn(['revoked_at', 'last_viewed_at', 'revoke_reason']);
        });
    }
};
