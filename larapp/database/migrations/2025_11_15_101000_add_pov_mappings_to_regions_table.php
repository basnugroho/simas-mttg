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
        Schema::table('regions', function (Blueprint $table) {
            if (! Schema::hasColumn('regions', 'pov_mappings')) {
                $table->json('pov_mappings')->nullable()->after('type_key')->comment('JSON object storing per-POV parent mappings and equivalents');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('regions', function (Blueprint $table) {
            if (Schema::hasColumn('regions', 'pov_mappings')) {
                $table->dropColumn('pov_mappings');
            }
        });
    }
};
