<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create pivot table for many-to-many mosque-subsidiary relationship
        Schema::create('mosque_subsidiary', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mosque_id');
            $table->unsignedBigInteger('subsidiary_id');
            $table->timestamps();

            $table->foreign('mosque_id')->references('id')->on('mosques')->onDelete('cascade');
            $table->foreign('subsidiary_id')->references('id')->on('subsidiaries')->onDelete('cascade');
            $table->unique(['mosque_id', 'subsidiary_id']);
        });

        // Migrate existing subsidiary_id data to pivot table
        DB::statement("
            INSERT INTO mosque_subsidiary (mosque_id, subsidiary_id, created_at, updated_at)
            SELECT id, subsidiary_id, NOW(), NOW()
            FROM mosques
            WHERE subsidiary_id IS NOT NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mosque_subsidiary');
    }
};
