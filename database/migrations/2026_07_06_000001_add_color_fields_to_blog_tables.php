<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blog_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('blog_categories', 'color')) {
                $table->string('color')->default('indigo')->after('slug');
            }
            if (!Schema::hasColumn('blog_categories', 'icon')) {
                $table->string('icon')->nullable()->after('color');
            }
            if (!Schema::hasColumn('blog_categories', 'description')) {
                $table->text('description')->nullable()->after('icon');
            }
        });

        Schema::table('blog_posts', function (Blueprint $table) {
            if (!Schema::hasColumn('blog_posts', 'reading_time')) {
                $table->unsignedTinyInteger('reading_time')->default(5)->after('views_count');
            }
            if (!Schema::hasColumn('blog_posts', 'featured_image')) {
                $table->string('featured_image')->nullable()->after('content');
            }
            if (!Schema::hasColumn('blog_posts', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('featured_image');
            }
        });
    }

    public function down(): void
    {
        Schema::table('blog_categories', function (Blueprint $table) {
            $columns = ['color', 'icon', 'description'];
            foreach ($columns as $col) {
                if (Schema::hasColumn('blog_categories', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('blog_posts', function (Blueprint $table) {
            if (Schema::hasColumn('blog_posts', 'reading_time')) {
                $table->dropColumn('reading_time');
            }
            if (Schema::hasColumn('blog_posts', 'featured_image')) {
                $table->dropColumn('featured_image');
            }
            if (Schema::hasColumn('blog_posts', 'is_featured')) {
                $table->dropColumn('is_featured');
            }
        });
    }
};
