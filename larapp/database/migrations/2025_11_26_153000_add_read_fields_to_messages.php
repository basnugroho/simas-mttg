<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('messages')) {
            Schema::table('messages', function (Blueprint $table) {
                if (!Schema::hasColumn('messages', 'is_read')) {
                    $table->boolean('is_read')->default(false)->after('message');
                }
                if (!Schema::hasColumn('messages', 'read_at')) {
                    $table->timestamp('read_at')->nullable()->after('is_read');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('messages')) {
            Schema::table('messages', function (Blueprint $table) {
                if (Schema::hasColumn('messages', 'read_at')) {
                    $table->dropColumn('read_at');
                }
                if (Schema::hasColumn('messages', 'is_read')) {
                    $table->dropColumn('is_read');
                }
            });
        }
    }
};
