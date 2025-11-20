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
            $table->string('witel_new')->nullable()->after('witel_id');
            $table->foreignId('subsidiary_id')->nullable()->after('witel_new')
                ->constrained('subsidiaries')
                ->nullOnDelete();
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
            if (Schema::hasColumn('mosques', 'subsidiary_id')) {
                $table->dropForeign([ 'subsidiary_id' ]);
                $table->dropColumn('subsidiary_id');
            }
            if (Schema::hasColumn('mosques', 'witel_new')) {
                $table->dropColumn('witel_new');
            }
        });
    }
};
