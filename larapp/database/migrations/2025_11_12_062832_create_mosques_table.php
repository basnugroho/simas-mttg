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
        Schema::create('mosques', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name', 150);
            $table->string('code', 50)->nullable();
            $table->enum('type', ['MASJID', 'MUSHOLLA'])->default('MASJID');
            $table->string('address', 255)->nullable();

            $table->unsignedBigInteger('province_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->unsignedBigInteger('witel_id')->nullable();

            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->string('image_url', 255)->nullable();
            $table->text('description')->nullable();

            $table->unsignedTinyInteger('completion_percentage')->default(0);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->foreign('province_id')->references('id')->on('regions')->nullOnDelete();
            $table->foreign('city_id')->references('id')->on('regions')->nullOnDelete();
            $table->foreign('witel_id')->references('id')->on('regions')->nullOnDelete();

            $table->index(['province_id', 'city_id', 'witel_id']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mosques');
    }
};
