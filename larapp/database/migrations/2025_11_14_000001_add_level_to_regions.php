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
        Schema::table('regions', function (Blueprint $table) {
            if (! Schema::hasColumn('regions', 'level')) {
                $table->string('level', 20)->default('OTHER')->after('type');
            }
        });

        // Map existing `type` values to new `level` semantics
        // Assumption mapping:
        // - PROVINCE -> REGIONAL
        // - WITEL -> WITEL
        // - CITY -> STO
        DB::table('regions')->where('type', 'PROVINCE')->update(['level' => 'REGIONAL']);
        DB::table('regions')->where('type', 'WITEL')->update(['level' => 'WITEL']);
        DB::table('regions')->where('type', 'CITY')->update(['level' => 'STO']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('regions', function (Blueprint $table) {
            if (Schema::hasColumn('regions', 'level')) {
                $table->dropColumn('level');
            }
        });
    }
};
