<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('cash_positions', function (Blueprint $table) {
            $table->boolean('is_deleted')->default(false)->after('nominal');
            $table->unsignedBigInteger('edited_by')->nullable()->after('is_deleted')->index();
            $table->timestamp('edited_at')->nullable()->after('edited_by');
            $table->text('edit_note')->nullable()->after('edited_at');
        });
    }

    public function down()
    {
        Schema::table('cash_positions', function (Blueprint $table) {
            $table->dropColumn(['is_deleted','edited_by','edited_at','edit_note']);
        });
    }
};
