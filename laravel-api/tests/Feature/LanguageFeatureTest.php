<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Language;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LanguageFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Ensure roles exist for every test
        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    }

    /** Helper to create a user with a specific role */
    private function createUser(UserRole $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role->value);
        return $user;
    }

    // --- PUBLIC ACCESS TESTS ---

    public function test_anyone_can_list_languages()
    {
        Language::factory()->count(3)->create();

        $response = $this->getJson('/api/languages');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_anyone_can_view_a_single_language()
    {
        $language = Language::factory()->create(['name' => 'Finnish', 'code' => 'fi']);

        $response = $this->getJson("/api/languages/{$language->id}");

        $response->assertStatus(200)
            ->assertJsonPath('name', 'Finnish');
    }

    // --- ROLE-BASED ACCESS TESTS (Edge Cases) ---

    public function test_student_cannot_create_language()
    {
        $student = $this->createUser(UserRole::STUDENT);

        $response = $this->actingAs($student)
            ->postJson('/api/languages', ['name' => 'French', 'code' => 'fr']);

        $response->assertStatus(403);
    }

    public function test_teacher_can_create_language()
    {
        $teacher = $this->createUser(UserRole::TEACHER);

        $response = $this->actingAs($teacher)
            ->postJson('/api/languages', [
                'name' => 'German',
                'code' => 'de'
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('name', 'German');
    }

    public function test_teacher_cannot_delete_language()
    {
        $teacher = $this->createUser(UserRole::TEACHER);
        $language = Language::factory()->create();

        $response = $this->actingAs($teacher)
            ->deleteJson("/api/languages/{$language->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('languages', ['id' => $language->id]);
    }

    public function test_admin_can_delete_language()
    {
        $admin = $this->createUser(UserRole::ADMIN);
        $language = Language::factory()->create();

        $response = $this->actingAs($admin)
            ->deleteJson("/api/languages/{$language->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('languages', ['id' => $language->id]);
    }

    // --- VALIDATION & DATA INTEGRITY TESTS ---

    public function test_cannot_create_language_with_duplicate_code()
    {
        $admin = $this->createUser(UserRole::ADMIN);
        Language::factory()->create(['code' => 'fi']);

        $response = $this->actingAs($admin)
            ->postJson('/api/languages', [
                'name' => 'Finnish New',
                'code' => 'fi' // Duplicate
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['code']);
    }

    public function test_update_fails_if_name_is_already_taken_by_another_record()
    {
        $admin = $this->createUser(UserRole::ADMIN);
        Language::factory()->create(['name' => 'English', 'code' => 'en']);
        $target = Language::factory()->create(['name' => 'Swedish', 'code' => 'sv']);

        $response = $this->actingAs($admin)
            ->patchJson("/api/languages/{$target->id}", [
                'name' => 'English', // Already taken by ID 1
                'code' => 'sv'
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_update_succeeds_when_keeping_the_same_name_for_the_same_id()
    {
        $admin = $this->createUser(UserRole::ADMIN);
        $language = Language::factory()->create(['name' => 'English', 'code' => 'en']);

        $response = $this->actingAs($admin)
            ->patchJson("/api/languages/{$language->id}", [
                'name' => 'English', // Same name, should be ignored by unique rule
                'code' => 'en'
            ]);

        $response->assertStatus(200);
    }

    public function test_returns_404_if_language_not_found()
    {
        $response = $this->getJson('/api/languages/999');

        $response->assertStatus(404);
    }
}
