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
        if (\Illuminate\Support\Facades\DB::getDriverName() !== 'sqlite') {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE enrollments MODIFY COLUMN currency ENUM('CAD', 'USD', 'GBP', 'EUR', 'EGP') DEFAULT 'CAD'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE enrollments MODIFY COLUMN currency ENUM('CAD', 'USD', 'GBP') DEFAULT 'CAD'");
    }
};
