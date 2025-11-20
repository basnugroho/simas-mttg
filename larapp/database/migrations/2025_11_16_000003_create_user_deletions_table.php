<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_deletions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('deleted_user_id')->index();
            $table->unsignedBigInteger('deleted_by_user_id')->nullable()->index();
            $table->json('payload')->nullable();
            $table->timestamp('deleted_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_deletions');
    }
};
