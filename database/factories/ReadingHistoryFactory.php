<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\{User, Book, ReadingHistory};

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ReadingHistory>
 */
class ReadingHistoryFactory extends Factory
{
    protected $model = ReadingHistory::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'book_id' => Book::factory(),
            'is_read' => fake()->randomElement([1, 0]),
        ];
    }
}
