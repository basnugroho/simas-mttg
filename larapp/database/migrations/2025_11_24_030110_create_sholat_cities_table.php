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
        Schema::create('sholat_cities', function (Blueprint $table) {
            $table->id();
            $table->string('api_id')->index();   // id dari MyQuran, contoh: 1601, 1701, dll
            $table->string('name');              // nama kota/kab dari API
            $table->string('province');          // JAWA TIMUR, BALI, NTB, NTT
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sholat_cities');
    }
};
