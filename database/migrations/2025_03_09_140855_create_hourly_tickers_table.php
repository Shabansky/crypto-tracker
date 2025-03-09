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
        Schema::create('hourly_tickers', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->float('price')->default(null)->nullable(true);
            $table->dateTime('time')->default(null)->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hourly_tickers');
    }
};
