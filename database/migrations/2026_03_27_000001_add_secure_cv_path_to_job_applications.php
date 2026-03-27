<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            // Thêm cột lưu đường dẫn file CV riêng tư (không public)
            // Cột cv_file cũ vẫn giữ để tương thích ngược
            $table->string('cv_path')->nullable()->after('cv_file');

            // Thêm index cho tìm kiếm bảo mật
            $table->index(['job_post_id', 'user_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropIndex(['job_post_id', 'user_id']);
            $table->dropIndex(['status']);
            $table->dropColumn('cv_path');
        });
    }
};
