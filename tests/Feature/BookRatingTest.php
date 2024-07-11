<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\{Book, User};

class BookRatingTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    public function test_user_can_add_rating_to_book_data(): void
    {
        $user = User::factory()->create();
        $user->assignRole($this->faker->randomElement(['admin', 'user']));

        $book = Book::factory()->create();

        $id = $book->id;
        $user_id = $user->id;
        $rating = $this->faker->numberBetween(1, 5);

        $response = $this->actingAs($user)->json('POST', route('rating'), [
            'id' => $id,
            'user_id' => $user_id,
            'rating' => $rating,
        ]);

        $response->assertSuccessful();
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => ['title', 'ratings', 'rating'],
            'status'
        ]);
        $response->assertJson([
            'data' => [
                'id' => $id,
                'ratings' => $response['data']['ratings'],
                'rating' => $response['data']['rating'],
            ]
        ]);

        $this->assertDatabaseHas('books', [
            'id' => $id,
        ]);
    }

    public function test_user_can_change_their_rating_to_book_data(): void
    {
        $book = Book::factory()->create();
        $user = User::findOrFail(collect($book->ratings)->pluck('user_id')->toArray()[0]);
        $user->assignRole($this->faker->randomElement(['admin', 'user']));

        $current_rating = collect($book->ratings)->pluck('rating')->toArray()[0];

        $id = $book->id;
        $user_id = $user->id;
        $rating = $current_rating === 1 ? $current_rating + 1 : $current_rating - 1;

        $response = $this->actingAs($user)->json('POST', route('rating'), [
            'id' => $id,
            'user_id' => $user_id,
            'rating' => $rating,
        ]);

        $response->assertSuccessful();
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => ['title', 'ratings', 'rating'],
            'status'
        ]);
        $response->assertJson([
            'data' => [
                'id' => $id,
                'ratings' => $response['data']['ratings'],
                'rating' => $response['data']['rating'],
            ]
        ]);

        $this->assertDatabaseHas('books', [
            'id' => $id,
        ]);
    }

    public function test_non_auth_user_cannot_add_rating_to_book_data(): void
    {
        $book = Book::factory()->create();

        $id = $book->id;
        $user_id = $this->faker->numberBetween(1, 1000);
        $rating = $this->faker->numberBetween(1, 5);

        $response = $this->json('POST', route('rating'), [
            'id' => $id,
            'user_id' => $user_id,
            'rating' => $rating,
        ]);

        $response->assertStatus(401);
    }
}
