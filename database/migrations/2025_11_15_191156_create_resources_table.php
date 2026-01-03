<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('teacher_id');
            $table->unsignedBigInteger('student_id')->nullable(); // لو المورد خاص لطالب
            $table->unsignedBigInteger('course_id')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->enum('type', ['pdf', 'image', 'video', 'audio', 'link', 'other'])->default('other');
            $table->string('file_path')->nullable(); // or URL
            $table->string('mime_type')->nullable();
            $table->string('external_url')->nullable(); // لو link
            $table->timestamps();

            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};
