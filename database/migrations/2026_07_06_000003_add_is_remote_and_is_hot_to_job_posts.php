<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_posts', function (Blueprint $table) {
            if (!Schema::hasColumn('job_posts', 'is_remote')) {
                $table->boolean('is_remote')->default(false)->after('experience_level');
            }
            if (!Schema::hasColumn('job_posts', 'is_hot')) {
                $table->boolean('is_hot')->default(false)->after('is_remote');
            }
        });
    }

    public function down(): void
    {
        Schema::table('job_posts', function (Blueprint $table) {
            foreach (['is_remote', 'is_hot'] as $col) {
                if (Schema::hasColumn('job_posts', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
