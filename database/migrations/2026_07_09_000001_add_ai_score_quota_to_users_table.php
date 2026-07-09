<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * L4: Thêm quota AI score cho mỗi user — chống spam đốt tiền OpenAI.
     *
     *   - ai_score_used_total: tổng AI score đã dùng (giới hạn cứng all-time)
     *   - ai_score_used_daily: số lần chấm trong ngày (reset mỗi ngày)
     *   - ai_score_reset_at: ngày reset daily counter (YYYY-MM-DD)
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('ai_score_used_total')->default(0);
            $table->unsignedInteger('ai_score_used_daily')->default(0);
            $table->date('ai_score_reset_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['ai_score_used_total', 'ai_score_used_daily', 'ai_score_reset_at']);
        });
    }
};