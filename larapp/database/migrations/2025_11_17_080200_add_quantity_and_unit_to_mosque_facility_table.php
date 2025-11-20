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
        Schema::table('mosque_facility', function (Blueprint $table) {
            // quantity for numeric facilities (nullable)
            $table->unsignedInteger('quantity')->nullable()->after('note');
            // optional override unit on the assignment (nullable)
            $table->unsignedBigInteger('unit_id')->nullable()->after('quantity');
            $table->foreign('unit_id')->references('id')->on('facility_units')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mosque_facility', function (Blueprint $table) {
            $table->dropForeign(['unit_id']);
            $table->dropColumn(['unit_id', 'quantity']);
        });
    }
};
