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
        Schema::table('enrollments', function (Blueprint $table) {
            // Remove old payment tracking fields (now tracked monthly in enrollment_payments table)
            $table->dropColumn([
                'amount',
                'payment_status',
                'payment_due_date',
                'paid_at',
                'payment_notes'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            // Restore old payment fields
            $table->decimal('amount', 10, 2)->nullable()->after('status');
            $table->enum('payment_status', ['paid', 'unpaid', 'partial'])->default('unpaid')->after('amount');
            $table->date('payment_due_date')->nullable()->after('payment_status');
            $table->date('paid_at')->nullable()->after('payment_due_date');
            $table->text('payment_notes')->nullable()->after('paid_at');
        });
    }
};
