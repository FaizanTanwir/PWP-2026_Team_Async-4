<?php

namespace Tests\Unit\Models;

use App\Enums\UserRole;
use App\Models\Course;
use App\Models\Sentence;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UnitTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed roles to prevent RoleDoesNotExist errors
        Role::create(['name' => UserRole::STUDENT->value]);
    }

    public function test_user_can_be_assigned_a_role(): void
    {
        $user = User::factory()->create();
        $user->assignRole(UserRole::STUDENT->value);

        $this->assertTrue($user->hasRole(UserRole::STUDENT->value));
    }

    public function test_user_can_generate_api_tokens(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $this->assertNotNull($token);
        $this->assertCount(1, $user->tokens);
    }

    public function test_user_password_is_hidden_in_json(): void
    {
        $user = User::factory()->create();

        $this->assertArrayNotHasKey('password', $user->toArray());
    }
}
