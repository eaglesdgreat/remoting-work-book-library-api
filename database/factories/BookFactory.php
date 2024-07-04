<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\{Author, Book};

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    protected $model = Book::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()-> sentence(10),
            'subtitle' => fake()->sentence(20),
            'description' => fake()->sentence(75),
            'author_id' => Author::factory(),
            'image_url' => fake()->imageUrl(),
            'number_of_pages' => fake()->numberBetween(100, 1000),
            'publisher' => fake()->name(),
            'published_date' => fake()->date(),
            'language' => fake()->languageCode(),
            'ratings' => fake()->numberBetween(1, 5),
            'book_url' => fake()->url(),
        ];
    }
}
