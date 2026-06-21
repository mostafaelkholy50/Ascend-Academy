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
        $columnExists = function (string $columnName): bool {
            return Schema::hasColumn('enrollments', $columnName);
        };

        $dropIndexIfExists = function (string $indexName) {
            if (DB::getDriverName() === 'sqlite') {
                return; // SQLite doesn't support dropping indices easily without recreating table, or we just skip it for in-memory tests
            }
            try {
                Schema::table('enrollments', function (Blueprint $table) use ($indexName) {
                    $table->dropIndex($indexName);
                });
            } catch (\Exception $e) {
                // Ignore if index doesn't exist
            }
        };

        $dropIndexIfExists('enrollments_billing_cycle_start_index');
        $dropIndexIfExists('enrollments_billing_cycle_end_index');
        $dropIndexIfExists('enrollments_billing_cycle_start_billing_cycle_end_index');

        $columnsToDrop = array_values(array_filter(
            ['end_date', 'billing_cycle_start', 'billing_cycle_end'],
            fn (string $column) => $columnExists($column)
        ));

        if (!empty($columnsToDrop)) {
            Schema::table('enrollments', function (Blueprint $table) use ($columnsToDrop) {
                $table->dropColumn($columnsToDrop);
            });
        }

        // Backfill any null values before tightening the column constraint.
        DB::table('enrollments')
            ->whereNull('start_date')
            ->update(['start_date' => now()->toDateString()]);

        Schema::table('enrollments', function (Blueprint $table) {
            // MySQL does not accept CURRENT_DATE as a default in this ALTER syntax.
            $table->date('start_date')->nullable(false)->change();
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
