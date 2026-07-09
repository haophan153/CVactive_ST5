<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * L3: Loại bỏ cột `color` legacy trong templates.
     *
     * Trước đây: cùng 1 template có 2 field `color` + `theme_color` cùng mục đích.
     * View đọc `color` qua accessor getColorStyleAttribute(), nhưng seeder ghi cả 2.
     * → Inconsistent khi admin edit template (chỉ save 1 field) mà UI cần field kia.
     *
     * Giờ: chỉ giữ `theme_color` (hex), `color` chỉ dùng trong accessor như
     * semantic alias cho theme_color palette name (indigo/emerald/...).
     */
    public function up(): void
    {
        if (Schema::hasColumn('templates', 'color')) {
            Schema::table('templates', function (Blueprint $table) {
                $table->dropColumn('color');
            });
        }
    }

    public function down(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            $table->string('color', 30)->nullable()->after('theme_color');
        });
    }
};