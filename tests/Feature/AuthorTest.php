<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\{Author, User};

class AuthorTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    public function test_admin_user_can_view_all_authors_data(): void
    {
        $user = User::findOrFail(1);

        $response = $this->actingAs($user)->json('GET', route('authors.index'));

        $response->assertSuccessful();
        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => [['name', 'about']], 'status']);
    }

    public function test_non_admin_user_cannot_view_all_authors_data(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->json('GET', route('authors.index'));

        $response->assertForbidden();
        $response->assertStatus(403);
    }

    public function test_admin_user_can_create_author_data(): void
    {
        $user = User::findOrFail(1);

        $name = $this->faker->name;
        $about = $this->faker->sentence(70);
        $summary = $this->faker->sentence(10);

        $response = $this->actingAs($user)->json('POST', route('authors.store'), [
            'name' => $name,
            'about' => $about,
            'summary' => $summary,
        ]);

        $response->assertSuccessful();
        $response->assertStatus(201);
        $response->assertJsonStructure(['data' => ['name', 'about', 'summary'], 'status']);
        $response->assertJson([
            'data' => [
                'name' => $name,
                'about' => $about,
                'summary' => $summary,
            ]
        ]);

        $this->assertDatabaseHas('authors', [
            'name' => $name,
            'id' => $response['data']['id'],
        ]);
    }

    public function test_non_admin_user_cannot_create_author_data(): void
    {
        $user = User::factory()->create();

        $name = $this->faker->name;
        $about = $this->faker->sentence(30);
        $summary = $this->faker->sentence(5);

        $response = $this->actingAs($user)->json('POST', route('authors.store'), [
            'name' => $name,
            'about' => $about,
            'summary' => $summary,
        ]);

        $response->assertStatus(403);
        $response->assertForbidden();
    }

    public function test_admin_user_cannot_create_author_data_without_require_fields(): void
    {
        $user = User::findOrFail(1);

        $name = "";
        $about = $this->faker->sentence(70);
        $summary = $this->faker->sentence(10);

        $response = $this->actingAs($user)->json('POST', route('authors.store'), [
            'name' => $name,
            'about' => $about,
            'summary' => $summary,
        ]);

        $response->assertUnprocessable();
        $response->assertJsonStructure(['errors']);
    }

    public function test_auth_user_can_get_author_data(): void
    {
        $user = User::factory()->create();
        $user->assignRole($this->faker->randomElement(['admin', 'user']));
        $author = Author::factory()->create();

        $response = $this->actingAs($user)->json('GET', route('authors.show', ['author' => $author->id]));

        $response->assertOk();
        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['name', 'about'], 'status']);
    }

    public function test_admin_user_can_update_author_data(): void
    {
        $user = User::findOrFail(1);
        $author = Author::factory()->create();

        $name = $this->faker->name;
        $about = $this->faker->sentence(20);
        $summary = $this->faker->sentence(4);

        $response = $this->actingAs($user)->json('PUT', route('authors.update', ['author' => $author->id]), [
            'name' => $name,
            'about' => $about,
            'summary' => $summary,
        ]);

        $response->assertSuccessful();
        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['name', 'about', 'summary'], 'status']);
        $response->assertJson([
            'data' => [
                'name' => $name,
                'about' => $about,
                'summary' => $summary,
            ]
        ]);

        $this->assertDatabaseHas('authors', [
            'name' => $name,
            'id' => $author->id,
        ]);
    }

    public function test_non_admin_user_cannot_update_author_data(): void
    {
        $user = User::factory()->create();
        $author = Author::factory()->create();

        $name = $this->faker->name;
        $about = $this->faker->sentence(20);
        $summary = $this->faker->sentence(4);

        $response = $this->actingAs($user)->json('PUT', route('authors.update', ['author' => $author->id]), [
            'name' => $name,
            'about' => $about,
            'summary' => $summary,
        ]);

        $response->assertStatus(403);
        $response->assertForbidden();
    }

    public function test_admin_user_cannot_update_author_data_without_require_fields(): void
    {
        $user = User::findOrFail(1);
        $author = Author::factory()->create();

        $name = $this->faker->name;
        $about = "";
        $summary = $this->faker->sentence(10);

        $response = $this->actingAs($user)->json('PUT', route('authors.update', ['author' => $author->id]), [
            'name' => $name,
            'about' => $about,
            'summary' => $summary,
        ]);

        $response->assertUnprocessable();
        $response->assertJsonStructure(['errors']);
    }

    public function test_admin_user_can_delete_author_data(): void
    {
        $user = User::findOrFail(1);
        $author = Author::factory()->create();

        $response = $this->actingAs($user)->json('DELETE', route('authors.destroy', ['author' => $author->id]));

        $response->assertSuccessful();
        $response->assertStatus(204);
        $response->assertNoContent();

        $this->assertDatabaseMissing('authors', [
            'id' => $author->id,
            'name' => $author->name,
        ]);
    }

    public function test_non_admin_user_cannot_delete_author_data(): void
    {
        $user = User::factory()->create();
        $author = Author::factory()->create();

        $response = $this->actingAs($user)->json('DELETE', route('authors.destroy', ['author' => $author->id]));

        $response->assertForbidden();
        $response->assertStatus(403);
    }
}
