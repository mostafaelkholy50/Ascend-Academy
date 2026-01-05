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
        Schema::table('inquiries', function (Blueprint $table) {
            // New fields
            $table->date('join_date')->nullable()->after('message');
            $table->integer('age')->nullable()->after('child_age');
            $table->string('study_hours')->nullable()->after('age');
            $table->string('courses_needed')->nullable()->after('preferred_course'); // Can store multiple as comma separated or string
            $table->string('sessions_per_week')->nullable()->after('courses_needed');
            $table->json('available_days')->nullable()->after('sessions_per_week');
            $table->string('referrer')->nullable()->after('available_days');
            $table->enum('gender', ['male', 'female'])->nullable()->after('child_gender');
            $table->string('city_state')->nullable()->after('city');

            // Make phone required as requested
            $table->string('phone')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->dropColumn([
                'join_date',
                'age',
                'study_hours',
                'courses_needed',
                'sessions_per_week',
                'available_days',
                'referrer',
                'gender',
                'city_state',
            ]);

            $table->string('phone')->nullable()->change();
        });
    }
};
