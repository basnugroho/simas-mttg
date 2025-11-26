<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('cash_position_photos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cash_position_id')->index();
            $table->string('path');
            $table->integer('sort_order')->default(0);
            $table->string('caption')->nullable();
            $table->timestamps();
            $table->foreign('cash_position_id')->references('id')->on('cash_positions')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cash_position_photos');
    }
};
