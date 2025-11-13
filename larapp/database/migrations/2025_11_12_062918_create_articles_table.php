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
        Schema::create('articles', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('title', 200);
            $table->string('slug', 200)->unique();

            $table->unsignedBigInteger('category_id')->nullable();

            $table->text('summary')->nullable();
            $table->longText('content')->nullable();

            $table->string('image_url', 255)->nullable();

            $table->timestamp('published_at')->nullable();

            $table->enum('status', ['DRAFT', 'PUBLISHED'])->default('DRAFT');

            $table->timestamps();

            $table->foreign('category_id')
                ->references('id')->on('categories')
                ->nullOnDelete();

            $table->index('status');
            $table->index('published_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
