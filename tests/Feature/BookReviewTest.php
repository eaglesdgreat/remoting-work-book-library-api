<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\{Review, User, Book};

class BookReviewTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    public function test_user_can_view_all_book_reviews_data(): void
    {
        $review = Review::factory()->create();
        $user = User::findOrFail($review->user_id);
        $user->assignRole($this->faker->randomElement(['admin', 'user']));

        $response = $this->actingAs($user)->json('GET', route('reviews.index', [
            'first' => 3,
            'page' => 1,
            'user_id' => $review->user_id,
            'book_id' => $review->book_id
        ]));

        $response->assertSuccessful();
        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => [['comment', 'user_id', 'book_id']], 'paginatorInfo', 'status']);
    }

    public function test_user_can_create_book_review_data(): void
    {
        $user = User::factory()->create();
        $user->assignRole($this->faker->randomElement(['admin', 'user']));

        $book = Book::factory()->create();

        $comment = $this->faker-> word;
        $user_id = $user->id;
        $book_id = $book->id;

        $response = $this->actingAs($user)->json('POST', route('reviews.store'), [
            'comment' => $comment,
            'user_id' => $user_id,
            'book_id' => $book_id,
        ]);

        $response->assertSuccessful();
        $response->assertStatus(201);
        $response->assertJsonStructure(['data' => ['comment', 'book_id', 'user_id'], 'status']);
        $response->assertJson([
            'data' => [
               'comment' => $comment,
                'user_id' => $user_id,
                'book_id' => $book_id,
            ]
        ]);

        $this->assertDatabaseHas('reviews', [
            'comment' => $comment,
            'user_id' => $user_id,
            'book_id' => $book_id,
        ]);
    }

    public function test_user_cannot_create_book_review_data_without_required_fields(): void
    {
        $user = User::factory()->create();
        $user->assignRole($this->faker->randomElement(['admin', 'user']));

        $book = Book::factory()->create();

        $comment = "";
        $user_id = $user->id;
        $book_id = $book->id;

        $response = $this->actingAs($user)->json('POST', route('reviews.store'), [
            'comment' => $comment,
            'user_id' => $user_id,
            'book_id' => $book_id,
        ]);

        $response->assertUnprocessable();
        $response->assertJsonStructure(['errors']);
    }

    public function test_non_auth_user_cannot_create_book_review_data(): void
    {
        $user = User::factory()->create();
        $user->assignRole($this->faker->randomElement(['admin', 'user']));

        $book = Book::factory()->create();

        $comment = $this->faker-> word;
        $user_id = $user->id;
        $book_id = $book->id;

        $response = $this->json('POST', route('reviews.store'), [
            'comment' => $comment,
            'user_id' => $user_id,
            'book_id' => $book_id,
        ]);

        $response->assertStatus(401);
    }

    public function test_user_can_get_a_book_review_data(): void
    {
        $user = User::factory()->create();
        $user->assignRole($this->faker->randomElement(['admin', 'user']));

        $review = Review::factory()->create();

        $response = $this->actingAs($user)->json('GET', route('reviews.show', ['review' => $review->id]));

        $response->assertOk();
        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['comment', 'book_id', 'user_id'], 'status']);
    }

    public function test_user_can_update_their_book_review_data(): void
    {
        $review = Review::factory()->create();
        $user = User::findOrFail($review->user_id);
        $user->assignRole($this->faker->randomElement(['admin', 'user']));

        $comment = $this->faker-> word;

        $response = $this->actingAs($user)->json('PUT', route('reviews.update', ['review' => $review->id]), ['comment' => $comment]);

        $response->assertSuccessful();
        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['comment', 'book_id', 'user_id'], 'status']);
        $response->assertJson(['data' => ['comment' => $comment]]);

        $this->assertDatabaseHas('reviews', ['id' => $review->id, 'comment' => $comment]);
    }

    public function test_user_cannot_update_their_book_review_data_without_required_fields(): void
    {
        $review = Review::factory()->create();
        $user = User::findOrFail($review->user_id);
        $user->assignRole($this->faker->randomElement(['admin', 'user']));

        $comment = "";

        $response = $this->actingAs($user)->json('PUT', route('reviews.update', ['review' => $review->id]), ['comment' => $comment]);

        $response->assertUnprocessable();
        $response->assertJsonStructure(['errors']);
    }

    public function test_admin_user_can_delete_book_review_data(): void
    {
        $user = User::findOrFail(1);
        $review = Review::factory()->create();

        $response = $this->actingAs($user)->json('DELETE', route('reviews.destroy', ['review' => $review->id]));

        $response->assertSuccessful();
        $response->assertStatus(204);
        $response->assertNoContent();

        $this->assertDatabaseMissing('reviews', [
            'id' => $review->id,
            'user_id' => $review->user_id,
            'book_id' => $review->book_id,
        ]);
    }

    public function test_non_admin_user_cannot_delete_book_review_data(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');
        $review = Review::factory()->create();

        $response = $this->actingAs($user)->json('DELETE', route('reviews.destroy', ['review' => $review->id]));

        $response->assertForbidden();
        $response->assertStatus(403);
    }
}
