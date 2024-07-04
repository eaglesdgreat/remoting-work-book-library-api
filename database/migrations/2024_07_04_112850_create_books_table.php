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
            $table->string('image_url');
            $table->integer('number_of_pages')->nullable();
            $table->string('publisher');
            $table->date('published_date');
            $table->string('language')->nullable();
            $table->jsonb('ratings')->nullable();
            $table->string('book_url');
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
