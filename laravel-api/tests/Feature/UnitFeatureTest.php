<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Course;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UnitFeatureTest extends TestCase
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

    // --- PUBLIC ACCESS ---

    public function test_anyone_can_list_units()
    {
        $course = Course::factory()->create();
        Unit::factory()->count(3)->create();

        $this->getJson("/api/courses/{$course->id}/units")
            ->assertStatus(200)
            ->assertJsonCount(3);
    }

    // --- CREATION LOGIC ---

    public function test_teacher_can_create_unit_for_their_own_course()
    {
        $teacher = $this->createUser(UserRole::TEACHER);
        $course = Course::factory()->create(['created_by_id' => $teacher->id]);

        $payload = [
            'title' => 'Grammar Basics',
            'course_id' => $course->id,
        ];

        $this->actingAs($teacher)
            ->postJson("/api/courses/{$course->id}/units", $payload)
            ->assertStatus(201);

        $this->assertDatabaseHas('units', ['title' => 'Grammar Basics', 'course_id' => $course->id]);
    }

    public function test_teacher_cannot_create_unit_for_another_teachers_course()
    {
        $teacherA = $this->createUser(UserRole::TEACHER);
        $teacherB = $this->createUser(UserRole::TEACHER);
        $courseOfA = Course::factory()->create(['created_by_id' => $teacherA->id]);

        $this->actingAs($teacherB)
            ->postJson("/api/courses/{$courseOfA->id}/units", [
                'title' => 'Unauthorized Unit',
                'course_id' => $courseOfA->id
            ])
            ->assertStatus(403);
    }

    // --- UPDATE & DELETE (Ownership Checks) ---

    public function test_teacher_can_patch_their_own_unit()
    {
        $teacher = $this->createUser(UserRole::TEACHER);
        $course = Course::factory()->create(['created_by_id' => $teacher->id]);
        $unit = Unit::factory()->create(['course_id' => $course->id]);

        $this->actingAs($teacher)
            ->patchJson("/api/units/{$unit->id}", ['title' => 'Updated Unit Title'])
            ->assertStatus(200);

        $this->assertDatabaseHas('units', ['id' => $unit->id, 'title' => 'Updated Unit Title']);
    }

    public function test_teacher_cannot_patch_another_teachers_unit()
    {
        $teacherA = $this->createUser(UserRole::TEACHER);
        $teacherB = $this->createUser(UserRole::TEACHER);
        $courseOfA = Course::factory()->create(['created_by_id' => $teacherA->id]);
        $unitOfA = Unit::factory()->create(['course_id' => $courseOfA->id]);

        $this->actingAs($teacherB)
            ->patchJson("/api/units/{$unitOfA->id}", ['title' => 'Hack Attempt'])
            ->assertStatus(403);
    }

    public function test_admin_can_delete_any_unit()
    {
        $admin = $this->createUser(UserRole::ADMIN);
        $unit = Unit::factory()->create();

        $this->actingAs($admin)
            ->deleteJson("/api/units/{$unit->id}")
            ->assertStatus(204);

        $this->assertDatabaseMissing('units', ['id' => $unit->id]);
    }

    public function test_teacher_cannot_delete_another_teachers_unit()
    {
        $teacherA = $this->createUser(UserRole::TEACHER);
        $teacherB = $this->createUser(UserRole::TEACHER);
        $courseOfA = Course::factory()->create(['created_by_id' => $teacherA->id]);
        $unitOfA = Unit::factory()->create(['course_id' => $courseOfA->id]);

        $this->actingAs($teacherB)
            ->deleteJson("/api/units/{$unitOfA->id}")
            ->assertStatus(403);
    }

    // --- VALIDATION ---

    public function test_create_unit_fails_if_course_does_not_exist()
    {
        $admin = $this->createUser(UserRole::ADMIN);

        $this->actingAs($admin)
            ->postJson("/api/courses/9999/units", [
                'title' => 'Ghost Unit',
                'course_id' => 9999
            ])
            ->assertStatus(404);
    }
}
