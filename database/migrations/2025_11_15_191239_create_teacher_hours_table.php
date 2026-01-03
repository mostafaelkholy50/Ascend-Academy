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
        Schema::create('teacher_hours', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('teacher_id');
            $table->year('year');  // السنة
            $table->tinyInteger('month'); // الشهر (1-12)
            $table->decimal('total_hours', 8, 2)->default(0);
            $table->decimal('hourly_rate', 10, 2); // سعر الساعة
            $table->decimal('total_salary', 12, 2)->default(0); // total_hours * hourly_rate
            $table->text('notes')->nullable();
            $table->boolean('is_paid')->default(false); // اتدفع ولا لسه؟
            $table->date('paid_at')->nullable();
            $table->timestamps();

            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['teacher_id', 'year', 'month']); // سطر واحد لكل مدرس كل شهر
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_hours');
    }
};
