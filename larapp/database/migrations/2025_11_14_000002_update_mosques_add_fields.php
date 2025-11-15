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
            // new fields requested
            if (! Schema::hasColumn('mosques', 'tahun_didirikan')) {
                $table->unsignedSmallInteger('tahun_didirikan')->nullable()->after('address');
            }
            if (! Schema::hasColumn('mosques', 'jml_bkm')) {
                $table->unsignedInteger('jml_bkm')->default(0)->after('tahun_didirikan');
            }
            if (! Schema::hasColumn('mosques', 'luas_tanah')) {
                $table->decimal('luas_tanah', 10, 2)->nullable()->after('jml_bkm')->comment('Satuan m2');
            }
            if (! Schema::hasColumn('mosques', 'daya_tampung')) {
                $table->unsignedInteger('daya_tampung')->nullable()->after('luas_tanah');
            }

            // relation columns to regions: regional / witel / sto
            if (! Schema::hasColumn('mosques', 'regional_id')) {
                $table->unsignedBigInteger('regional_id')->nullable()->after('daya_tampung');
            }
            if (! Schema::hasColumn('mosques', 'witel_id')) {
                $table->unsignedBigInteger('witel_id')->nullable()->after('regional_id');
            }
            if (! Schema::hasColumn('mosques', 'sto_id')) {
                $table->unsignedBigInteger('sto_id')->nullable()->after('witel_id');
            }

            // add foreign keys (null-on-delete)
            if (! Schema::hasColumn('mosques', 'regional_id')) {
                // nothing
            }
        });

        // Add foreign keys in separate statements to avoid issues on some DB drivers
        Schema::table('mosques', function (Blueprint $table) {
            // only add foreign keys if columns exist and keys not present
            try {
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                // we don't check existing foreign key names here; attempt add with try/catch
                $table->foreign('regional_id')->references('id')->on('regions')->nullOnDelete();
                $table->foreign('witel_id')->references('id')->on('regions')->nullOnDelete();
                $table->foreign('sto_id')->references('id')->on('regions')->nullOnDelete();
            } catch (\Throwable $e) {
                // best-effort: if adding foreign keys fails (e.g., already exists), ignore
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mosques', function (Blueprint $table) {
            if (Schema::hasColumn('mosques', 'sto_id')) {
                // drop foreign first if exists
                try { $table->dropForeign(['sto_id']); } catch (\Throwable $e) {}
                $table->dropColumn('sto_id');
            }
            if (Schema::hasColumn('mosques', 'witel_id')) {
                try { $table->dropForeign(['witel_id']); } catch (\Throwable $e) {}
                $table->dropColumn('witel_id');
            }
            if (Schema::hasColumn('mosques', 'regional_id')) {
                try { $table->dropForeign(['regional_id']); } catch (\Throwable $e) {}
                $table->dropColumn('regional_id');
            }

            if (Schema::hasColumn('mosques', 'daya_tampung')) {
                $table->dropColumn('daya_tampung');
            }
            if (Schema::hasColumn('mosques', 'luas_tanah')) {
                $table->dropColumn('luas_tanah');
            }
            if (Schema::hasColumn('mosques', 'jml_bkm')) {
                $table->dropColumn('jml_bkm');
            }
            if (Schema::hasColumn('mosques', 'tahun_didirikan')) {
                $table->dropColumn('tahun_didirikan');
            }
        });
    }
};
