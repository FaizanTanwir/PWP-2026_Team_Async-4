<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Course;
use App\Models\Sentence;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SentenceFeatureTest extends TestCase
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

    // --- ACCESS CONTROL (All protected by Sanctum) ---

    public function test_guest_cannot_access_any_sentence_route()
    {
        $unit = Unit::factory()->create();
        $this->getJson("/api/units/{$unit->id}/sentences")->assertStatus(401);
    }

    public function test_authenticated_user_can_list_unit_sentences()
    {
        $user = $this->createUser(UserRole::STUDENT);
        $unit = Unit::factory()->create();

        Sentence::factory()->count(3)->create(['unit_id' => $unit->id, 'user_id' => $user->id]);

        $this->actingAs($user)
            ->getJson("/api/units/{$unit->id}/sentences")
            ->assertStatus(200)
            ->assertJsonCount(3);
    }

    // --- CREATION LOGIC ---

    public function test_teacher_can_create_sentence_in_their_own_course_unit()
    {
        $teacher = $this->createUser(UserRole::TEACHER);
        $course = Course::factory()->create(['created_by_id' => $teacher->id]);
        $unit = Unit::factory()->create(['course_id' => $course->id]);

        $payload = [
            'text_target' => 'Minä asun Oulussa',
            'text_source' => 'I live in Oulu',
            'unit_id' => $unit->id,
            'user_id' => $teacher->id
        ];

        $this->actingAs($teacher)
            ->postJson('/api/sentences', $payload)
            ->assertStatus(201)
            ->assertJsonPath('user_id', $teacher->id);
    }

    public function test_teacher_cannot_create_sentence_in_another_teachers_unit()
    {
        $teacherA = $this->createUser(UserRole::TEACHER);
        $teacherB = $this->createUser(UserRole::TEACHER);

        $courseA = Course::factory()->create(['created_by_id' => $teacherA->id]);
        $unitA = Unit::factory()->create(['course_id' => $courseA->id]);

        $this->actingAs($teacherB)
            ->postJson('/api/sentences', [
                'text_target' => 'Unauthorized',
                'text_source' => 'Unauthorized',
                'unit_id' => $unitA->id
            ])
            ->assertStatus(403);
    }

    // --- UPDATE & DELETE (The Short-Path Checks) ---

    public function test_teacher_can_update_their_own_sentence()
    {
        $teacher = $this->createUser(UserRole::TEACHER);
        $sentence = Sentence::factory()->create(['user_id' => $teacher->id]);

        $this->actingAs($teacher)
            ->patchJson("/api/sentences/{$sentence->id}", [
                'text_target' => 'Updated Text'
            ])
            ->assertStatus(200);

        $this->assertDatabaseHas('sentences', ['id' => $sentence->id, 'text_target' => 'Updated Text']);
    }

    public function test_teacher_cannot_update_another_teachers_sentence()
    {
        $teacherA = $this->createUser(UserRole::TEACHER);
        $teacherB = $this->createUser(UserRole::TEACHER);
        $sentenceA = Sentence::factory()->create(['user_id' => $teacherA->id]);

        $this->actingAs($teacherB)
            ->patchJson("/api/sentences/{$sentenceA->id}", [
                'text_target' => 'Hacker edit'
            ])
            ->assertStatus(403);
    }

    public function test_admin_can_update_any_sentence()
    {
        $admin = $this->createUser(UserRole::ADMIN);
        $sentence = Sentence::factory()->create(); // Created by someone else

        $this->actingAs($admin)
            ->patchJson("/api/sentences/{$sentence->id}", ['text_target' => 'Admin fix'])
            ->assertStatus(200);
    }

    public function test_teacher_can_delete_their_own_sentence()
    {
        $teacher = $this->createUser(UserRole::TEACHER);
        $sentence = Sentence::factory()->create(['user_id' => $teacher->id]);

        $this->actingAs($teacher)
            ->deleteJson("/api/sentences/{$sentence->id}")
            ->assertStatus(204);

        $this->assertDatabaseMissing('sentences', ['id' => $sentence->id]);
    }

    // --- EDGE CASE: VALIDATION ---

    public function test_sentence_creation_fails_without_required_fields()
    {
        $admin = $this->createUser(UserRole::ADMIN);

        $this->actingAs($admin)
            ->postJson('/api/sentences', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['text_target', 'text_source', 'unit_id']);
    }
}
