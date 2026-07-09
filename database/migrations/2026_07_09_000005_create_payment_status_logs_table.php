<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * H-6: Audit log cho mọi lần admin thay đổi payment status.
 *
 * Bảng này bắt buộc để đáp ứng:
 *  - Revenue tampering forensics (ai flip status nào khi nào)
 *  - Fraud investigation (admin nào mark refunded/completed bất thường)
 *  - Audit trail cho kế toán (PCI-DSS style evidence trail)
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('payment_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained('payments')->onDelete('cascade');
            $table->foreignId('admin_id')->constrained('users')->onDelete('restrict');
            $table->string('old_status', 20)->nullable();
            $table->string('new_status', 20);
            $table->string('reason', 500);
            $table->string('ip', 64)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['payment_id', 'created_at']);
            $table->index(['admin_id', 'created_at']);
            $table->index('new_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_status_logs');
    }
};
