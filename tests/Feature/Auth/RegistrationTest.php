<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\User;

class RegistrationTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test a guest can register to the platform.
     */
    public function test_a_guest_can_register(): void
    {
        $response = $this->json('POST', route('register'), [
            'name' => 'Test Smith',
            'email' => 'test@example.com',
            'username' => 'test123',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertCreated();
        $response->assertJsonStructure(['data' => [
            'name', 'email',
        ], 'token']);
    }

    /**
     * Test a guest cannot register to the platform with email already exist.
     */
    public function test_a_guest_cannot_register_with_email_already_exist(): void
    {
        $user = User::factory()->create();

        $response = $this->json('POST', route('register'), [
            'name' => 'Test Smith',
            'email' => $user->email,
            'username' => 'test2123',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonStructure(['errors']);
    }

    /**
     * Test a guest cannot register to the platform with username already exist.
     */
    public function test_a_guest_cannot_register_with_username_already_exist(): void
    {
        $user = User::factory()->create();

        $response = $this->json('POST', route('register'), [
            'name' => 'Test Smith',
            'email' => 'test3@example.com',
            'username' => $user->username,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonStructure(['errors']);
    }
}
