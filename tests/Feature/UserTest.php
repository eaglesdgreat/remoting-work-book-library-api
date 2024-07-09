<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    public function test_admin_user_can_view_all_users_data(): void
    {
        $user = User::findOrFail(1);

        $response = $this->actingAs($user)->json('GET', route('users.index'));

        $response->assertSuccessful();
        $response->assertJsonStructure(['data' => [['name', 'email', 'username']]]);
    }

    public function test_non_admin_user_cannot_view_all_users_data(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->json('GET', route('users.index'));

        $response->assertForbidden();
        $response->assertStatus(403);
    }

    public function test_user_can_update_their_data(): void
    {
        $user = User::factory()->create();
        $name = $this->faker->name;
        $email = $this->faker->email();
        $username = $this->faker->userName();

        $response = $this->actingAs($user)->json('PUT', route('users.update', ['user' => $user->id]), [
            'name' => $name,
            'email' => $email,
            'username' => $username,
        ]);

        $response->assertSuccessful();
        $response->assertJsonStructure(['data' => ['name', 'email', 'username']]);
        $response->assertJson([
            'data' => [
                'name' => $name,
                'email' => $email,
                'username' => $username,
            ]
        ]);
    }

    public function test_user_cannot_update_their_data_without_email(): void
    {
        $user = User::factory()->create();
        $name = $this->faker->name;
        $email = "";
        $username = $this->faker->userName();

        $response = $this->actingAs($user)->json('PUT', route('users.update', ['user' => $user->id]), [
            'name' => $name,
            'email' => $email,
            'username' => $username,
        ]);

        $response->assertUnprocessable();
        $response->assertJsonStructure(['errors']);
    }
}
