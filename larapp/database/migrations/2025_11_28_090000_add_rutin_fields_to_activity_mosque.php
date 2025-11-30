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
        Schema::table('activity_mosque', function (Blueprint $table) {
            if (! Schema::hasColumn('activity_mosque', 'is_rutin')) {
                $table->boolean('is_rutin')->default(false)->after('event_end');
            }
            if (! Schema::hasColumn('activity_mosque', 'rutin_days')) {
                // store selected days as JSON array or comma-separated string; nullable
                $table->text('rutin_days')->nullable()->after('is_rutin');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_mosque', function (Blueprint $table) {
            if (Schema::hasColumn('activity_mosque', 'rutin_days')) {
                $table->dropColumn('rutin_days');
            }
            if (Schema::hasColumn('activity_mosque', 'is_rutin')) {
                $table->dropColumn('is_rutin');
            }
        });
    }
};
