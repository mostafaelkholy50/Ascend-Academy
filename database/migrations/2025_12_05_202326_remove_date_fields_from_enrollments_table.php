<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            // Drop indexes first (using correct names)
            $table->dropIndex('enrollments_billing_cycle_start_index');
            $table->dropIndex('enrollments_billing_cycle_end_index');
            $table->dropIndex('enrollments_billing_cycle_start_billing_cycle_end_index');
            
            // Remove columns
            $table->dropColumn(['end_date', 'billing_cycle_start', 'billing_cycle_end']);
            
            // Make start_date non-nullable with default value (current timestamp)
            $table->date('start_date')->nullable(false)->default(DB::raw('CURRENT_DATE'))->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            // Add columns back
            $table->date('end_date')->nullable()->after('start_date');
            $table->date('billing_cycle_start')->nullable()->after('end_date');
            $table->date('billing_cycle_end')->nullable()->after('billing_cycle_start');
            
            // Recreate indexes
            $table->index('billing_cycle_start');
            $table->index('billing_cycle_end');
            $table->index(['billing_cycle_start', 'billing_cycle_end']);
            
            // Make start_date nullable again
            $table->date('start_date')->nullable()->change();
        });
    }
};
