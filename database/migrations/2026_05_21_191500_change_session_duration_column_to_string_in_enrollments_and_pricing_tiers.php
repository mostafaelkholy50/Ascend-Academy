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
            $table->string('session_duration')->nullable()->change();
        });

        Schema::table('pricing_tiers', function (Blueprint $table) {
            $table->string('session_duration')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->enum('session_duration', ['30', '60'])->nullable()->change();
        });

        Schema::table('pricing_tiers', function (Blueprint $table) {
            $table->enum('session_duration', ['30', '60'])->change();
        });
    }
};
