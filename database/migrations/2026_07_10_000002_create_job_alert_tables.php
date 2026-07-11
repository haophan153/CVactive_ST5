<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── User job alert preferences ──────────────────────────────────────────
        Schema::create('user_job_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_active')->default(true);
            $table->unsignedTinyInteger('match_threshold')->default(60); // 1–100
            $table->enum('notification_frequency', ['daily', 'instant'])->default('daily');
            $table->json('preferred_categories')->nullable();    // ["it","design"]
            $table->json('preferred_job_types')->nullable();     // ["full-time","remote"]
            $table->json('preferred_locations')->nullable();     // ["Hà Nội","HCM"]
            $table->boolean('notify_new_jobs')->default(true);
            $table->boolean('notify_salary_up')->default(false);
            $table->datetime('last_sent_at')->nullable();
            $table->timestamps();

            $table->unique('user_id');
        });

        // ── Skill profile extracted from CV ────────────────────────────────────
        Schema::create('user_skill_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cv_id')->nullable()->nullOnDelete();
            $table->json('skills');                     // ["PHP","Laravel","Vue.js"]
            $table->json('job_titles')->nullable();    // ["Senior Developer","Tech Lead"]
            $table->json('companies')->nullable();       // ["FPT","Viettel"]
            $table->string('experience_level', 20)->nullable(); // fresher|junior|middle|senior|lead
            $table->json('preferred_categories')->nullable();
            $table->json('preferred_job_types')->nullable();
            $table->unsignedInteger('salary_expectation_min')->nullable();
            $table->unsignedInteger('salary_expectation_max')->nullable();
            $table->datetime('last_extracted_at')->nullable();
            $table->timestamps();

            $table->unique('user_id');
        });

        // ── Match history ─────────────────────────────────────────────────────
        Schema::create('job_match_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('job_post_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('rule_score')->default(0);   // 0–100 rule-based
            $table->unsignedTinyInteger('ai_score')->nullable();     // 0–100 AI (null = not scored)
            $table->json('matched_skills')->nullable();
            $table->json('missing_skills')->nullable();
            $table->datetime('sent_at')->nullable();       // null = matched but not sent yet
            $table->datetime('viewed_at')->nullable();
            $table->datetime('applied_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'job_post_id']);
            $table->index(['user_id', 'sent_at']);
            $table->index(['job_post_id', 'sent_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_match_logs');
        Schema::dropIfExists('user_skill_profiles');
        Schema::dropIfExists('user_job_alerts');
    }
};
