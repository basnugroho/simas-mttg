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
        if (! Schema::hasColumn('regions', 'level')) {
            Schema::table('regions', function (Blueprint $table) {
                $table->string('level')->nullable()->after('type_key');
            });

            // Backfill 'level' from existing type_key where we can reasonably map
            // Mapping heuristics (adjust as needed):
            // TREG / TREG_OLD -> REGIONAL
            // AREA -> AREA
            // WITEL / WITEL_OLD -> WITEL
            // STO -> STO
            DB::table('regions')->whereIn('type_key', ['TREG', 'TREG_OLD'])->update(['level' => 'REGIONAL']);
            DB::table('regions')->where('type_key', 'AREA')->update(['level' => 'AREA']);
            DB::table('regions')->whereIn('type_key', ['WITEL', 'WITEL_OLD'])->update(['level' => 'WITEL']);
            DB::table('regions')->where('type_key', 'STO')->update(['level' => 'STO']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('regions', 'level')) {
            Schema::table('regions', function (Blueprint $table) {
                $table->dropColumn('level');
            });
        }
    }
};
