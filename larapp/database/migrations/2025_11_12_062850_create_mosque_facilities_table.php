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
        Schema::create('mosque_facility', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('mosque_id');
            $table->unsignedBigInteger('facility_id');

            $table->boolean('is_available')->default(true);
            $table->string('note', 255)->nullable();

            $table->timestamps();

            $table->foreign('mosque_id')
                ->references('id')->on('mosques')
                ->onDelete('cascade');

            $table->foreign('facility_id')
                ->references('id')->on('facilities')
                ->onDelete('cascade');

            $table->unique(['mosque_id', 'facility_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mosque_facilities');
    }
};
