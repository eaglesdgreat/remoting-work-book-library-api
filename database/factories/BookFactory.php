<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\{Book, User};

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
            'title' => fake()-> sentence(4),
            'subtitle' => fake()->sentence(6),
            'description' => fake()->sentence(15),
            'image_url' => fake()->imageUrl(),
            'number_of_pages' => fake()->numberBetween(100, 1000),
            'publisher' => fake()->name(),
            'published_date' => fake()->date(),
            'language' => fake()->languageCode(),
            'ratings' => [
                [
                    'rating' => fake()->numberBetween(1, 5),
                    'user_id' => User::factory()->create()->id
                ]
            ],
            'book_url' => fake()->url(),
        ];
    }
}
