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
            $database = DB::getDatabaseName();

            return DB::table('information_schema.columns')
                ->where('table_schema', $database)
                ->where('table_name', 'enrollments')
                ->where('column_name', $columnName)
                ->exists();
        };

        $dropIndexIfExists = function (string $indexName) {
            $database = DB::getDatabaseName();
            $exists = DB::table('information_schema.statistics')
                ->where('table_schema', $database)
                ->where('table_name', 'enrollments')
                ->where('index_name', $indexName)
                ->exists();

            if ($exists) {
                Schema::table('enrollments', function (Blueprint $table) use ($indexName) {
                    $table->dropIndex($indexName);
                });
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
