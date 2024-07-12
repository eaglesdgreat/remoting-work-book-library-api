<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\{ReadingHistory, User, Book};

class ReadingHistoryTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

   public function test_user_can_add_book_to_their_history_data(): void
    {
        $user = User::factory()->create();
        $user->assignRole($this->faker->randomElement(['admin', 'user']));

        $book = Book::factory()->create();

        $response = $this->actingAs($user)->json('POST', route('reading_histories.store'), [
            'user_id' => $user->id,
            'book_id' => $book->id
        ]);

        $response->assertSuccessful();
        $response->assertStatus(201);

        $this->assertDatabaseHas('reading_histories', [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);
    }

    public function test_user_cannot_add_book_to_their_history_data_without_required_fields(): void
    {
        $user = User::factory()->create();
        $user->assignRole($this->faker->randomElement(['admin', 'user']));

        $response = $this->actingAs($user)->json('POST', route('reading_histories.store'), [
            'user_id' => $user->id,
            'book_id' => ""
        ]);

        $response->assertUnprocessable();
        $response->assertJsonStructure(['errors']);
    }

    public function test_user_can_mark_their_reading_history_as_read(): void
    {
        $reading_history = ReadingHistory::factory()->create();
        $user = User::findOrFail($reading_history->user_id);
        $user->assignRole($this->faker->randomElement(['admin', 'user']));

        $response = $this->actingAs($user)->json('PUT', route('reading_histories.update', ['reading_history' => $reading_history->id]), [
            'is_read' => true
        ]);

        $response->assertSuccessful();
        $response->assertStatus(200);

        $this->assertDatabaseHas('reading_histories', ['id' => $reading_history->id, 'is_read' => true]);
    }

    public function test_user_cannot_mark_their_reading_history_as_read_without_required_field(): void
    {
        $reading_history = ReadingHistory::factory()->create();
        $user = User::findOrFail($reading_history->user_id);
        $user->assignRole($this->faker->randomElement(['admin', 'user']));

        $response = $this->actingAs($user)->json('PUT', route('reading_histories.update', ['reading_history' => $reading_history->id]), [
            'is_read' => null
        ]);

        $response->assertUnprocessable();
        $response->assertJsonStructure(['errors']);
    }

    public function test_user_cannot_mark_reading_history_as_read_for_another_user(): void
    {
        $reading_history = ReadingHistory::factory()->create();
        $user = User::factory()->create();
        $user->assignRole($this->faker->randomElement(['admin', 'user']));

        $response = $this->actingAs($user)->json('PUT', route('reading_histories.update', ['reading_history' => $reading_history->id]), [
            'is_read' => true
        ]);

        $response->assertForbidden();
        $response->assertStatus(403);
    }
}
