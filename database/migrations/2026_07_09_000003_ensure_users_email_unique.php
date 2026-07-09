<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * H1: Dọn duplicate email + đảm bảo unique index tồn tại.
     *
     * Trước đây: chỉ dựa vào validation 'unique:users,email' ở controller.
     * Nếu 2 request POST /register cùng lúc với email "abc@x.com", cả 2
     * pass validation vì DB chưa có → cùng insert → DB không reject.
     *
     * Giờ: unique index từ DB layer → race condition không thể xảy ra.
     */
    public function up(): void
    {
        // Bước 1: gộp các account duplicate về 1 record cũ nhất
        DB::statement('
            DELETE u1 FROM users u1
            INNER JOIN users u2
            WHERE u1.id > u2.id
              AND u1.email = u2.email
              AND u1.email IS NOT NULL
              AND u1.email <> ""
        ');

        // Bước 2: thêm unique index nếu chưa có (an toàn với mọi DB driver)
        $indexExists = collect(DB::select("SHOW INDEXES FROM users WHERE Key_name = 'users_email_unique'"))->isNotEmpty();

        if (!$indexExists) {
            Schema::table('users', function (Blueprint $table) {
                $table->unique('email', 'users_email_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_email_unique');
        });
    }
};
