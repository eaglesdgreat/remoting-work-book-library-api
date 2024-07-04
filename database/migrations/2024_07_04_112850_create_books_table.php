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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->foreignId('author_id')->references('id')->on('authors');
            $table->char('description');
            $table->string('imageUrl');
            $table->integer('numberOfPages')->nullable();
            $table->string('publisher');
            $table->date('publishedDate');
            $table->string('language')->nullable();
            $table->jsonb('ratings')->nullable();
            $table->string('bookUrl');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
