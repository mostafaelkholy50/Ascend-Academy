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
        Schema::create('pricing_tiers', function (Blueprint $table) {
            $table->id();
            $table->integer('days_per_week'); // 1-7
            $table->enum('session_duration', ['30', '60']); // minutes
            $table->decimal('price_cad', 10, 2);
            $table->decimal('price_usd', 10, 2);
            $table->decimal('price_gbp', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            // Unique constraint: one pricing tier per days/duration combination
            $table->unique(['days_per_week', 'session_duration']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricing_tiers');
    }
};
