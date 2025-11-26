<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('mosque_managers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mosque_id')->index();
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->integer('jumlah_pengurus')->nullable();
            $table->string('ketua_pengurus')->nullable();
            $table->string('file_path')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('edited_by')->nullable();
            $table->timestamp('edited_at')->nullable();
            $table->text('edit_note')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
            $table->foreign('mosque_id')->references('id')->on('mosques')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('mosque_managers');
    }
};
