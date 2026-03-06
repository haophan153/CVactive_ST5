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
        Schema::create('cv_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cv_id')->constrained()->cascadeOnDelete();
            $table->string('share_token')->unique();
            $table->integer('view_count')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cv_shares');
    }
};
