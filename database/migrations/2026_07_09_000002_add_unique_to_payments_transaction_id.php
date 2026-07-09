<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * C4: Thêm unique constraint trên payments.transaction_id.
     *
     * Trước đây không có → return URL và IPN có thể race condition:
     * 2 process cùng complete 1 payment → user được nâng cấp 2 lần
     * (cộng dồn 2 tháng Pro khi chỉ trả tiền 1 lần).
     *
     * Với unique constraint: process thứ 2 fail với QueryException,
     * controller bắt được và không cộng dồn.
     */
    public function up(): void
    {
        // Bước 1: dọn duplicate nếu có (an toàn trên data đã có)
        \DB::statement('
            DELETE p1 FROM payments p1
            INNER JOIN payments p2
            WHERE p1.id > p2.id
              AND p1.transaction_id = p2.transaction_id
              AND p1.transaction_id IS NOT NULL
              AND p1.transaction_id <> ""
        ');

        Schema::table('payments', function (Blueprint $table) {
            // Bước 2: tạo unique index — DB tự reject duplicate khi race
            $table->unique('transaction_id', 'payments_transaction_id_unique');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropUnique('payments_transaction_id_unique');
        });
    }
};