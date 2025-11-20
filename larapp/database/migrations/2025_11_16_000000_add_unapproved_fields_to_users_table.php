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
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'unapproved_by')) {
                $table->unsignedBigInteger('unapproved_by')->nullable()->after('approved')->comment('user id who set approved=false');
            }
            if (! Schema::hasColumn('users', 'unapproved_at')) {
                $table->timestamp('unapproved_at')->nullable()->after('unapproved_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'unapproved_at')) {
                $table->dropColumn('unapproved_at');
            }
            if (Schema::hasColumn('users', 'unapproved_by')) {
                $table->dropColumn('unapproved_by');
            }
        });
    }
};
