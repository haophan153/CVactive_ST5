<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cv_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cv_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // e.g. experience, education, skills
            $table->string('title');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_visible')->default(true);
            $table->boolean('is_custom')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cv_sections');
    }
};
