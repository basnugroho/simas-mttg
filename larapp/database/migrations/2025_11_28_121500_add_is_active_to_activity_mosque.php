<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_mosque', function (Blueprint $table) {
            if (! Schema::hasColumn('activity_mosque', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('rutin_days');
            }
        });
    }

    public function down(): void
    {
        Schema::table('activity_mosque', function (Blueprint $table) {
            if (Schema::hasColumn('activity_mosque', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
};
