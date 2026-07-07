<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->unsignedTinyInteger('ai_score')->nullable()->after('notes');
            $table->text('ai_summary')->nullable()->after('ai_score');
            $table->json('ai_breakdown')->nullable()->after('ai_summary');
            $table->timestamp('ai_scored_at')->nullable()->after('ai_breakdown');

            $table->index(['job_post_id', 'ai_score']);
        });
    }

    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropIndex(['job_post_id', 'ai_score']);
            $table->dropColumn(['ai_score', 'ai_summary', 'ai_breakdown', 'ai_scored_at']);
        });
    }
};