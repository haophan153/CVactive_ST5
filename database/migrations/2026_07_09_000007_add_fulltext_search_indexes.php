<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * M-5: FULLTEXT index cho search trên job_posts + templates.
 *
 * Trước: `WHERE LOWER(col) LIKE '%q%'` → full table scan, DoS dễ dàng.
 * Sau: MySQL FULLTEXT index + boolean mode → O(log n).
 *
 * Lưu ý: chỉ chạy trên MySQL. SQLite không support native FULLTEXT
 * theo cách này (FTS5 riêng) nên skip qua exception.
 */
return new class extends Migration {
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            // Job posts: title + company + description + location
            DB::statement('ALTER TABLE job_posts ADD FULLTEXT INDEX ft_search (title, company_name, description, location)');
            // Templates: name only
            DB::statement('ALTER TABLE templates ADD FULLTEXT INDEX ft_templates_name (name)');
            // Blog posts: title + excerpt + content
            if (Schema::hasTable('blog_posts')) {
                DB::statement('ALTER TABLE blog_posts ADD FULLTEXT INDEX ft_blog_search (title, excerpt, content)');
            }
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE job_posts DROP INDEX ft_search');
            DB::statement('ALTER TABLE templates DROP INDEX ft_templates_name');
            if (Schema::hasTable('blog_posts')) {
                DB::statement('ALTER TABLE blog_posts DROP INDEX ft_blog_search');
            }
        }
    }
};
