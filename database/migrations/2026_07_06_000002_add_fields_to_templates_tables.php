<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('template_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('template_categories', 'icon')) {
                $table->string('icon')->nullable()->after('slug');
            }
            if (!Schema::hasColumn('template_categories', 'color')) {
                $table->string('color')->default('indigo')->after('icon');
            }
            if (!Schema::hasColumn('template_categories', 'description')) {
                $table->text('description')->nullable()->after('color');
            }
        });

        Schema::table('templates', function (Blueprint $table) {
            if (!Schema::hasColumn('templates', 'theme_color')) {
                $table->string('theme_color', 20)->default('#4F46E5')->after('is_active');
            }
            if (!Schema::hasColumn('templates', 'color')) {
                $table->string('color', 20)->default('indigo')->after('theme_color');
            }
        });
    }

    public function down(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            if (Schema::hasColumn('templates', 'theme_color')) $table->dropColumn('theme_color');
            if (Schema::hasColumn('templates', 'color')) $table->dropColumn('color');
        });
        Schema::table('template_categories', function (Blueprint $table) {
            foreach (['icon', 'color', 'description'] as $col) {
                if (Schema::hasColumn('template_categories', $col)) $table->dropColumn($col);
            }
        });
    }
};
