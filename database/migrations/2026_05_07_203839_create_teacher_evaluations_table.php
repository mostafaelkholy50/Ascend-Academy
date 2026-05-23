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
        Schema::create('teacher_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('evaluator_id')->constrained('users')->onDelete('cascade');
            $table->date('evaluation_date');
            $table->date('week_start_date');
            
            // Scores
            $table->unsignedTinyInteger('q1_score')->default(0)->comment('Discipline and Punctuality (15)');
            $table->unsignedTinyInteger('q2_score')->default(0)->comment('General Appearance (10)');
            $table->unsignedTinyInteger('q3_score')->default(0)->comment('Environment and Setup (10)');
            $table->unsignedTinyInteger('q4_score')->default(0)->comment('Calmness and Focus (15)');
            $table->unsignedTinyInteger('q5_score')->default(0)->comment('Explanation Efficiency (30)');
            $table->unsignedTinyInteger('q6_score')->default(0)->comment('Interaction with Student (20)');
            
            $table->unsignedTinyInteger('total_score')->default(0)->comment('Total out of 100');
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Ensure only one evaluation per teacher per week
            $table->unique(['teacher_id', 'week_start_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_evaluations');
    }
};
