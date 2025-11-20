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
        Schema::table('mosques', function (Blueprint $table) {
            if (!Schema::hasColumn('mosques', 'area_id')) {
                // keep it nullable so existing rows are unaffected; avoid foreign key to prevent FK migrations issues
                $table->unsignedBigInteger('area_id')->nullable()->after('regional_id')->index();
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
        Schema::table('mosques', function (Blueprint $table) {
            if (Schema::hasColumn('mosques', 'area_id')) {
                if (Schema::hasColumn('mosques', 'area_id')) {
                    // drop index first if exists
                    try { $table->dropIndex(['area_id']); } catch (\Throwable $e) { }
                }
                $table->dropColumn('area_id');
            }
        });
    }
};
