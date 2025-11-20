<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Drop the province and city columns from mosques if present.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('mosques')) {
            Schema::table('mosques', function (Blueprint $table) {
                if (Schema::hasColumn('mosques', 'province')) {
                    $table->dropColumn('province');
                }
                if (Schema::hasColumn('mosques', 'city')) {
                    $table->dropColumn('city');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     * Recreate the province and city columns as nullable strings.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('mosques')) {
            Schema::table('mosques', function (Blueprint $table) {
                if (!Schema::hasColumn('mosques', 'province')) {
                    $table->string('province')->nullable()->after('address');
                }
                if (!Schema::hasColumn('mosques', 'city')) {
                    $table->string('city')->nullable()->after('province');
                }
            });
        }
    }
};
