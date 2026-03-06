<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cvs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('template_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->json('personal_info')->nullable();
            $table->text('objective')->nullable();
            $table->string('theme_color')->default('#4F46E5');
            $table->string('font_family')->default('Inter');
            $table->enum('visibility', ['public', 'private'])->default('private');
            $table->boolean('is_draft')->default(true);
            $table->timestamp('last_saved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cvs');
    }
};
