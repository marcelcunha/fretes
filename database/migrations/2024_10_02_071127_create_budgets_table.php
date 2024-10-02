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
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained();
            $table->float('quantity',2);
            $table->string('transport_type', 20);
            $table->string('loading_type', 20);
            $table->unsignedInteger('value')->default(0);
            $table->foreignId('origin_id')->constrained('locations');
            $table->foreignId('destination_id')->constrained('locations');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
