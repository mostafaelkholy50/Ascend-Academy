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
        Schema::create('teacher_applications', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('country');
            $table->string('city')->nullable();
            $table->enum('gender', ['male', 'female']);
            $table->date('birth_date')->nullable();

            // Qualifications
            $table->string('education_level'); // Bachelor, Master, PhD, etc.
            $table->text('certifications')->nullable(); // Ijazah, Teaching certificates
            $table->integer('years_of_experience');
            $table->text('teaching_experience'); // Description of experience

            // Teaching Details
            $table->json('subjects'); // ['Quran', 'Tajweed', 'Arabic', 'Islamic Studies']
            $table->json('age_groups'); // ['kids', 'teens', 'adults']
            $table->text('teaching_methodology')->nullable();
            $table->json('availability'); // Days and times available

            // Technical
            $table->boolean('has_stable_internet')->default(true);
            $table->boolean('has_quiet_space')->default(true);
            $table->text('why_join')->nullable();

            // CV/Resume
            $table->string('cv_path')->nullable();

            // Status
            $table->enum('status', ['pending', 'reviewed', 'approved', 'rejected', 'converted'])->default('pending');
            $table->text('admin_notes')->nullable();

            $table->timestamps();
            $table->index(['status', 'email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_applications');
    }
};
