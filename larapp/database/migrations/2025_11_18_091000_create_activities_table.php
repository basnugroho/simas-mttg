<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('activity_name');
            // category: mahdhah (ritual/worship) or ghairu mahdhah (non-ritual)
            $table->enum('category', ['mahdhah', 'ghairu_mahdhah'])->default('ghairu_mahdhah');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            // master activities table; mosque associations are stored in pivot table
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activities');
    }
};
