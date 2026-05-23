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
        Schema::create('student_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('course_id')->nullable()->constrained('courses')->onDelete('set null');
            
            $table->date('evaluation_date');
            $table->unsignedTinyInteger('evaluation_month');
            $table->unsignedSmallInteger('evaluation_year');
            
            // Scores 1-10
            $table->unsignedTinyInteger('q1_score')->default(0)->comment('Attendance & Punctuality');
            $table->unsignedTinyInteger('q2_score')->default(0)->comment('Participation & Engagement');
            $table->unsignedTinyInteger('q3_score')->default(0)->comment('Homework Completion');
            $table->unsignedTinyInteger('q4_score')->default(0)->comment('Understanding & Comprehension');
            $table->unsignedTinyInteger('q5_score')->default(0)->comment('Behavior & Discipline');
            $table->unsignedTinyInteger('q6_score')->default(0)->comment('Focus & Attention');
            $table->unsignedTinyInteger('q7_score')->default(0)->comment('Interaction with Teacher');
            $table->unsignedTinyInteger('q8_score')->default(0)->comment('Progress & Improvement');
            $table->unsignedTinyInteger('q9_score')->default(0)->comment('Effort & Motivation');
            $table->unsignedTinyInteger('q10_score')->default(0)->comment('Retention of Previous Lessons');
            
            $table->unsignedTinyInteger('total_score')->default(0)->comment('Total out of 100');
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Unique constraint: one evaluation per student per teacher per month
            $table->unique(['teacher_id', 'student_id', 'evaluation_month', 'evaluation_year'], 'student_eval_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_evaluations');
    }
};
