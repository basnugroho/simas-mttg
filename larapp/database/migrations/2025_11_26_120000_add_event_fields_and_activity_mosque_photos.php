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
            if (! Schema::hasColumn('activity_mosque', 'event_start')) {
                $table->timestamp('event_start')->nullable()->after('note');
            }
            if (! Schema::hasColumn('activity_mosque', 'event_end')) {
                $table->timestamp('event_end')->nullable()->after('event_start');
            }
        });

        Schema::create('activity_mosque_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_mosque_id')->constrained('activity_mosque')->cascadeOnDelete();
            $table->string('path');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_mosque', function (Blueprint $table) {
            if (Schema::hasColumn('activity_mosque', 'event_end')) {
                $table->dropColumn('event_end');
            }
            if (Schema::hasColumn('activity_mosque', 'event_start')) {
                $table->dropColumn('event_start');
            }
        });

        Schema::dropIfExists('activity_mosque_photos');
    }
};
