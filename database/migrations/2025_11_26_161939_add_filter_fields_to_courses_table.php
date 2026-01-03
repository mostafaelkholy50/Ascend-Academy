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
        Schema::table('courses', function (Blueprint $table) {
            $table->enum('level', ['Beginner', 'Intermediate', 'Advanced'])->nullable()->after('price');
            $table->enum('age_group', ['Kids', 'Teens', 'Adults'])->nullable()->after('level');
            $table->enum('language', ['English', 'Arabic'])->nullable()->after('age_group');
            $table->boolean('is_free')->default(false)->after('language');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['level', 'age_group', 'language', 'is_free']);
        });
    }
};
