<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('articles', function (Blueprint $table) {
            if (!Schema::hasColumn('articles', 'mosque_id')) {
                $table->unsignedBigInteger('mosque_id')->nullable()->after('category_id')->index();
            }
            if (!Schema::hasColumn('articles', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('mosque_id')->index();
            }
        });

        Schema::table('articles', function (Blueprint $table) {
            try {
                $table->foreign('mosque_id')->references('id')->on('mosques')->nullOnDelete();
                $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            } catch (\Throwable $e) {
                // best effort: if FK addition fails (e.g., already exists), ignore
            }
        });
    }

    public function down()
    {
        Schema::table('articles', function (Blueprint $table) {
            if (Schema::hasColumn('articles', 'created_by')) {
                try { $table->dropForeign(['created_by']); } catch (\Throwable $e) {}
                $table->dropColumn('created_by');
            }
            if (Schema::hasColumn('articles', 'mosque_id')) {
                try { $table->dropForeign(['mosque_id']); } catch (\Throwable $e) {}
                $table->dropColumn('mosque_id');
            }
        });
    }
};
