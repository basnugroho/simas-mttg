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
        Schema::table('mosques', function (Blueprint $table) {
            $table->string('bank_name')->nullable()->after('daya_tampung')->comment('Nama Bank');
            $table->string('bank_account_name')->nullable()->after('bank_name')->comment('Atas Nama Rekening');
            $table->string('bank_account_number')->nullable()->after('bank_account_name')->comment('Nomor Rekening');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mosques', function (Blueprint $table) {
            $table->dropColumn(['bank_name', 'bank_account_name', 'bank_account_number']);
        });
    }
};
