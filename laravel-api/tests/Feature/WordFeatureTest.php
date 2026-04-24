<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\Word;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class WordFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    }

    private function createUser(UserRole $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role->value);
        return $user;
    }

    // --- UPDATE TESTS (Teachers & Admins) ---

    public function test_teacher_can_update_a_word()
    {
        $teacher = $this->createUser(UserRole::TEACHER);
        $word = Word::factory()->create([
            'term' => 'initial',
            'translation' => 'alkuperäinen'
        ]);

        $this->actingAs($teacher)
            ->patchJson("/api/words/{$word->id}", [
                'translation' => 'updated'
            ])
            ->assertStatus(200);

        $this->assertDatabaseHas('words', [
            'id' => $word->id,
            'translation' => 'updated'
        ]);
    }

    public function test_admin_can_update_a_word()
    {
        $admin = $this->createUser(UserRole::ADMIN);
        $word = Word::factory()->create();

        $this->actingAs($admin)
            ->patchJson("/api/words/{$word->id}", [
                'term' => 'AdminEdited'
            ])
            ->assertStatus(200);
    }

    public function test_student_cannot_update_a_word()
    {
        $student = $this->createUser(UserRole::STUDENT);
        $word = Word::factory()->create();

        $this->actingAs($student)
            ->patchJson("/api/words/{$word->id}", [
                'term' => 'Hack'
            ])
            ->assertStatus(403);
    }

    // --- DELETE TESTS (Admin ONLY) ---

    public function test_admin_can_delete_a_word()
    {
        $admin = $this->createUser(UserRole::ADMIN);
        $word = Word::factory()->create();

        $this->actingAs($admin)
            ->deleteJson("/api/words/{$word->id}")
            ->assertStatus(204);

        $this->assertDatabaseMissing('words', ['id' => $word->id]);
    }

    public function test_teacher_cannot_delete_a_word()
    {
        $teacher = $this->createUser(UserRole::TEACHER);
        $word = Word::factory()->create();

        $this->actingAs($teacher)
            ->deleteJson("/api/words/{$word->id}")
            ->assertStatus(403);

        $this->assertDatabaseHas('words', ['id' => $word->id]);
    }

    // --- VALIDATION & EDGE CASES ---

    public function test_update_validation_rules()
    {
        $admin = $this->createUser(UserRole::ADMIN);
        $word = Word::factory()->create();

        // Testing 'sometimes' - providing an empty string for term should fail if you have 'required'
        // but here 'string' and 'max' are the constraints.
        $this->actingAs($admin)
            ->patchJson("/api/words/{$word->id}", [
                'term' => str_repeat('a', 256) // Over max:255
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['term']);
    }

    public function test_update_handles_nullable_fields()
    {
        $admin = $this->createUser(UserRole::ADMIN);
        $word = Word::factory()->create(['lemma' => 'not-null']);

        $this->actingAs($admin)
            ->patchJson("/api/words/{$word->id}", [
                'lemma' => null
            ])
            ->assertStatus(200);

        $this->assertDatabaseHas('words', [
            'id' => $word->id,
            'lemma' => null
        ]);
    }
}
