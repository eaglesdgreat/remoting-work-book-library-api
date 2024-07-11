<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\{Book, User, Author};
use Illuminate\Http\UploadedFile;

class BookTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    public function test_user_can_view_all_books_data(): void
    {
        $response = $this->json('GET', route('books.index', [
            'first' => 20,
            'page' => 1,
        ]));

        $response->assertSuccessful();
        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => [['title', 'book_url', 'authors', 'image_url']], 'paginatorInfo', 'status']);
    }

    public function test_user_can_view_all_books_data_with_filter(): void
    {
        $book = Book::factory()->create();

        $response = $this->json('GET', route('books.index', [
            'first' => 3,
            'page' => 1,
            'filter' => [[
                'column' => 'language',
                'operator' => '=',
                'value' => $book->language
            ]]
        ]));

        $response->assertSuccessful();
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [['language', 'book_url', 'authors', 'title']],
            'paginatorInfo',
            'status'
        ]);
        $response->assertJson([
            'data' => [[
                'language' => $book->language,
            ]]
        ]);
    }

    public function test_user_can_view_all_books_data_with_sort(): void
    {
        $response = $this->json('GET', route('books.index', [
            'first' => 3,
            'page' => 1,
            'sort' => [[
                'column' => 'published_date',
                'order' => 'desc',
            ]]
        ]));

        $response->assertSuccessful();
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [['book_url', 'authors', 'title', 'published_date']],
            'paginatorInfo',
            'status'
        ]);
    }

    public function test_user_can_view_all_books_data_with_search(): void
    {
        $book = Book::factory()->create();

        $response = $this->json('GET', route('books.index', [
            'first' => 3,
            'page' => 1,
            'search' => $book->title,
        ]));

        $response->assertSuccessful();
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [['book_url', 'authors', 'title', 'published_date']],
            'paginatorInfo',
            'status'
        ]);
        $response->assertJson([
            'data' => [[
                'title' => $book->title,
            ]]
        ]);
    }

    public function test_admin_user_can_create_book_data(): void
    {
        $user = User::findOrFail(1);
        $authors = Author::inRandomOrder()->limit(3)->pluck('id');

        $title = $this->faker-> sentence(4);
        $subtitle = $this->faker->sentence(6);
        $description = $this->faker->sentence(15);
        $image = UploadedFile::fake()->image('image1.png', 600, 600);
        $number_of_pages = $this->faker->numberBetween(100, 1000);
        $publisher = $this->faker->name;
        $published_date = $this->faker->date();
        $language = $this->faker->languageCode();
        $book = UploadedFile::fake()->create($this->faker->word . '.pdf', 100);
        $author_ids = $authors;

        $response = $this->actingAs($user)->json('POST', route('books.store'), [
            'title' => $title,
            'subtitle' => $subtitle,
            'description' => $description,
            'image' => $image,
            'number_of_pages' => $number_of_pages,
            'publisher' => $publisher,
            'published_date' => $published_date,
            'language' => $language,
            'book' => $book,
            'author_ids' => $author_ids,
        ]);

        $response->assertSuccessful();
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => ['title', 'description', 'image_url', 'book_url', 'authors'],
            'status']
        );
        $response->assertJson([
            'data' => [
                'title' => $title,
                'subtitle' => $subtitle,
                'description' => $description,
                'image_url' => $response['data']['image_url'],
                'number_of_pages' => $number_of_pages,
                'publisher' => $publisher,
                'published_date' => $published_date,
                'language' => $language,
                'book_url' => $response['data']['book_url'],
            ]
        ]);

        $this->assertDatabaseHas('books', [
            'title' => $title,
            'subtitle' => $subtitle,
            'description' => $description,
            'image_url' => $response['data']['image_url'],
            'number_of_pages' => $number_of_pages,
            'publisher' => $publisher,
            'published_date' => $published_date,
            'language' => $language,
            'book_url' => $response['data']['book_url'],
        ]);
    }

    public function test_non_admin_user_cannot_create_book_data(): void
    {
        $user = User::factory()->create();
        $authors = Author::inRandomOrder()->limit(3)->pluck('id');

        $title = $this->faker-> sentence(4);
        $subtitle = $this->faker->sentence(6);
        $description = $this->faker->sentence(15);
        $image = UploadedFile::fake()->image('image1.png', 600, 600);
        $number_of_pages = $this->faker->numberBetween(100, 1000);
        $publisher = $this->faker->name;
        $published_date = $this->faker->date();
        $language = $this->faker->languageCode();
        $book = UploadedFile::fake()->create($this->faker->word . '.pdf', 100);
        $author_ids = $authors;

        $response = $this->actingAs($user)->json('POST', route('books.store'), [
            'title' => $title,
            'subtitle' => $subtitle,
            'description' => $description,
            'image' => $image,
            'number_of_pages' => $number_of_pages,
            'publisher' => $publisher,
            'published_date' => $published_date,
            'language' => $language,
            'book' => $book,
            'author_ids' => $author_ids,
        ]);

        $response->assertStatus(403);
        $response->assertForbidden();
    }

    public function test_admin_user_cannot_create_book_data_without_require_fields(): void
    {
        $user = User::findOrFail(1);

        $title = "";
        $subtitle = $this->faker->sentence(6);
        $description = $this->faker->sentence(15);
        $image = "";
        $number_of_pages = $this->faker->numberBetween(100, 1000);
        $publisher = $this->faker->name;
        $published_date = $this->faker->date();
        $language = $this->faker->languageCode();
        $book = UploadedFile::fake()->create($this->faker->word . '.pdf', 100);
        $author_ids = null;

        $response = $this->actingAs($user)->json('POST', route('books.store'), [
            'title' => $title,
            'subtitle' => $subtitle,
            'description' => $description,
            'image' => $image,
            'number_of_pages' => $number_of_pages,
            'publisher' => $publisher,
            'published_date' => $published_date,
            'language' => $language,
            'book' => $book,
            'author_ids' => $author_ids,
        ]);

        $response->assertUnprocessable();
        $response->assertJsonStructure(['errors']);
    }

    public function test_user_can_get_a_book_data(): void
    {
        $book = Book::factory()->create();

        $response = $this->json('GET', route('books.show', ['book' => $book->id]));

        $response->assertOk();
        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['title', 'book_url', 'authors', 'image_url'], 'status']);
    }

    public function test_admin_user_can_update_book_data(): void
    {
        $user = User::findOrFail(1);
        $book = Book::factory()->create();
        $authors = Author::inRandomOrder()->limit(3)->pluck('id');

        $title = $this->faker-> sentence(4);
        $description = $this->faker->sentence(15);
        $publisher = $this->faker->name;
        $published_date = $this->faker->date();
        $author_ids = $authors;

        $response = $this->actingAs($user)->json('PUT', route('books.update', ['book' => $book->id]), [
            'title' => $title,
            'description' => $description,
            'publisher' => $publisher,
            'published_date' => $published_date,
            'author_ids' => $author_ids,
        ]);

        $response->assertSuccessful();
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => ['title', 'description', 'image_url', 'book_url', 'authors'],
            'status']
        );
        $response->assertJson([
            'data' => [
                'title' => $title,
                'description' => $description,
                'publisher' => $publisher,
                'published_date' => $published_date,
            ]
        ]);

        $this->assertDatabaseHas('books', [
            'title' => $title,
            'description' => $description,
            'publisher' => $publisher,
            'published_date' => $published_date,
        ]);
    }

    public function test_non_admin_user_cannot_update_book_data(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $authors = Author::inRandomOrder()->limit(3)->pluck('id');

        $title = $this->faker-> sentence(4);
        $description = $this->faker->sentence(15);
        $publisher = $this->faker->name;
        $published_date = $this->faker->date();
        $author_ids = $authors;

        $response = $this->actingAs($user)->json('PUT', route('books.update', ['book' => $book->id]), [
            'title' => $title,
            'description' => $description,
            'publisher' => $publisher,
            'published_date' => $published_date,
            'author_ids' => $author_ids,
        ]);

        $response->assertStatus(403);
        $response->assertForbidden();
    }

    public function test_admin_user_cannot_update_book_data_without_require_fields(): void
    {
        $user = User::findOrFail(1);
        $book = Book::factory()->create();

        $title = "";
        $description = $this->faker->sentence(15);
        $publisher = $this->faker->name;
        $published_date = $this->faker->date();
        $author_ids = null;

        $response = $this->actingAs($user)->json('PUT', route('books.update', ['book' => $book->id]), [
            'title' => $title,
            'description' => $description,
            'publisher' => $publisher,
            'published_date' => $published_date,
            'author_ids' => $author_ids,
        ]);

        $response->assertUnprocessable();
        $response->assertJsonStructure(['errors']);
    }

    public function test_admin_user_can_delete_book_data(): void
    {
        $user = User::findOrFail(1);
        $book = Book::factory()->create();

        $response = $this->actingAs($user)->json('DELETE', route('books.destroy', ['book' => $book->id]));

        $response->assertSuccessful();
        $response->assertStatus(204);
        $response->assertNoContent();

        $this->assertDatabaseMissing('books', [
            'id' => $book->id,
            'title' => $book->title,
            'book_url' => $book->book_url,
        ]);
    }

    public function test_non_admin_user_cannot_delete_book_data(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $response = $this->actingAs($user)->json('DELETE', route('books.destroy', ['book' => $book->id]));

        $response->assertForbidden();
        $response->assertStatus(403);
    }
}
