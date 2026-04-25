<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup roles required for the register method
        Role::create(['name' => UserRole::STUDENT->value]);
    }

    /** -----------------------------------------------------------
     * REGISTRATION TESTS
     * ----------------------------------------------------------- */

    public function test_user_can_register_successfully(): void
    {
        $payload = [
            'name' => 'Mahtab Alam',
            'email' => 'mahtab@oulu.fi',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure(['user', 'token']);

        $this->assertDatabaseHas('users', ['email' => 'mahtab@oulu.fi']);

        // Verify role assignment
        $user = User::where('email', 'mahtab@oulu.fi')->first();
        $this->assertTrue($user->hasRole(UserRole::STUDENT->value));
    }

    public function test_registration_fails_with_duplicate_email(): void
    {
        User::factory()->create(['email' => 'test@example.com']);

        $payload = [
            'name' => 'New User',
            'email' => 'test@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ];

        $this->postJson('/api/register', $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_registration_fails_if_passwords_do_not_match(): void
    {
        $payload = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'DifferentPassword123!',
        ];

        $this->postJson('/api/register', $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /** -----------------------------------------------------------
     * LOGIN TESTS
     * ----------------------------------------------------------- */

    public function test_user_can_login_with_correct_credentials(): void
    {
        $password = 'Secret123!';
        $user = User::factory()->create([
            'password' => Hash::make($password)
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['user', 'token']);
    }

    public function test_login_fails_with_incorrect_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('CorrectPassword123!')
        ]);

        $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'WrongPassword',
        ])
        ->assertStatus(401)
        ->assertJson(['message' => 'Invalid credentials']);
    }

    /** -----------------------------------------------------------
     * LOGOUT TESTS
     * ----------------------------------------------------------- */

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        // Verify we can access the logout route with the token
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Successfully logged out']);

        // Verify the token is actually deleted from the database
        $this->assertEmpty($user->tokens);
    }

    public function test_unauthenticated_user_cannot_logout(): void
    {
        // Calling logout without a token/actingAs
        $this->postJson('/api/logout')
            ->assertStatus(401);
    }
}
