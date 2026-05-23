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
            // Flexible scheduling fields
            $table->integer('days_per_week')->nullable()->after('course_id');
            $table->enum('session_duration', ['30', '60'])->nullable()->after('days_per_week');
            
            // Admin-controlled pricing
            $table->decimal('admin_price', 10, 2)->nullable()->after('amount');
            $table->enum('currency', ['CAD', 'USD', 'GBP'])->default('CAD')->after('admin_price');
            
            // Monthly subscription billing
            $table->date('billing_cycle_start')->nullable()->after('end_date');
            $table->date('billing_cycle_end')->nullable()->after('billing_cycle_start');
            
            // Make amount nullable (will be replaced by admin_price)
            $table->decimal('amount', 10, 2)->nullable()->change();
            
            // Add indexes for performance
            $table->index('billing_cycle_start');
            $table->index('billing_cycle_end');
            $table->index(['billing_cycle_start', 'billing_cycle_end']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropIndex(['billing_cycle_start']);
            $table->dropIndex(['billing_cycle_end']);
            $table->dropIndex(['billing_cycle_start', 'billing_cycle_end']);
            
            $table->dropColumn([
                'days_per_week',
                'session_duration',
                'admin_price',
                'currency',
                'billing_cycle_start',
                'billing_cycle_end',
            ]);
        });
    }
};
