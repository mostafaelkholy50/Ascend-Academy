<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teacher_evaluations', function (Blueprint $table) {
            $table->integer('q7_score')->default(0)->after('q6_score');
            $table->integer('q8_score')->default(0)->after('q7_score');
            $table->integer('q9_score')->default(0)->after('q8_score');
            $table->integer('q10_score')->default(0)->after('q9_score');
        });
    }

    public function down(): void
    {
        Schema::table('teacher_evaluations', function (Blueprint $table) {
            $table->dropColumn(['q7_score', 'q8_score', 'q9_score', 'q10_score']);
        });
    }
};
