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
        Schema::create('enrollment_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('enrollment_id');
            $table->date('month'); // First day of the month
            $table->decimal('amount', 10, 2);
            $table->enum('currency', ['CAD', 'USD', 'GBP'])->default('CAD');
            $table->enum('payment_status', ['paid', 'unpaid', 'partial'])->default('unpaid');
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Foreign key
            $table->foreign('enrollment_id')->references('id')->on('enrollments')->onDelete('cascade');
            
            // Indexes
            $table->index('month');
            $table->index('payment_status');
            $table->unique(['enrollment_id', 'month']); // One payment record per enrollment per month
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollment_payments');
    }
};
