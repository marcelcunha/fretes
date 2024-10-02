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
        Schema::create('portages', function (Blueprint $table) {
            $table->id();
            $table->string('nf', 44)->nullable();
            $table->date('origin_date')->nullable();
            $table->date('delivery_date')->nullable();
            $table->unsignedInteger('value')->default(0);
            $table->foreignId('freight_id')->constrained();
            $table->foreignId('driver_id')->constrained();
            $table->foreignId('truck_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portages');
    }
};
