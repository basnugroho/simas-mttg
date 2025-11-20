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
        // This migration is safe to run even if the activities table already exists
        Schema::table('activities', function (Blueprint $table) {
            if (!Schema::hasColumn('activities', 'category')) {
                $table->enum('category', ['mahdhah', 'ghairu_mahdhah'])->default('ghairu_mahdhah')->after('activity_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('activities', function (Blueprint $table) {
            if (Schema::hasColumn('activities', 'category')) {
                $table->dropColumn('category');
            }
        });
    }
};
