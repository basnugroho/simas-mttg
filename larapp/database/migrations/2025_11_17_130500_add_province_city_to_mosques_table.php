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
            if (!Schema::hasColumn('mosques', 'province')) {
                $table->string('province')->nullable()->after('address');
            }
            if (!Schema::hasColumn('mosques', 'city')) {
                $table->string('city')->nullable()->after('province');
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
            if (Schema::hasColumn('mosques', 'city')) {
                $table->dropColumn('city');
            }
            if (Schema::hasColumn('mosques', 'province')) {
                $table->dropColumn('province');
            }
        });
    }
};
