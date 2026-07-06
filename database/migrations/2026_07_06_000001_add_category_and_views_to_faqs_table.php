<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('faqs', function (Blueprint $table) {
            $table->string('category')->default('general')->after('answer');
            $table->unsignedBigInteger('views_count')->default(0)->after('category');
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::table('faqs', function (Blueprint $table) {
            $table->dropIndex(['category']);
            $table->dropColumn(['category', 'views_count']);
        });
    }
};