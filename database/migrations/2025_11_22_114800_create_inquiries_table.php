<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inquiries', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['trial', 'contact', 'registration'])->default('trial');
            $table->string('full_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('child_name')->nullable();
            $table->string('child_age')->nullable();
            $table->enum('child_gender', ['male', 'female'])->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('preferred_course')->nullable();
            $table->text('message')->nullable();
            $table->enum('status', ['pending', 'contacted', 'converted', 'cancelled'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamps();

            $table->index(['type', 'status']);
            $table->index('email');
        });

        // Drop old trial_requests table
        Schema::dropIfExists('trial_requests');
    }

    public function down(): void
    {
        Schema::dropIfExists('inquiries');

        Schema::create('trial_requests', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('child_name')->nullable();
            $table->string('child_age')->nullable();
            $table->string('child_gender')->nullable();
            $table->string('Country')->nullable();
            $table->string('City')->nullable();
            $table->string('preferred_course')->nullable();
            $table->enum('status', ['pending', 'contacted', 'converted', 'cancelled'])->default('pending');
            $table->text('message')->nullable();
            $table->timestamps();
        });
    }
};
