<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Course;
use App\Models\Language;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class CourseFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);

        // Pre-create languages as they are required for courses
        Language::factory()->count(2)->create();
    }

    private function createUser(UserRole $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role->value);
        return $user;
    }

    // --- PUBLIC ACCESS ---

    public function test_anyone_can_list_courses()
    {
        $user = User::factory()->create();

        $sourceLang = Language::factory()->create();
        Course::factory()->count(2)->create([
            'source_language_id' => $sourceLang->id,
            'target_language_id' => Language::factory()->create()->id,
        ]);

        $this->actingAs($user)
            ->getJson("/api/languages/{$sourceLang->id}/courses")
            ->assertStatus(200)
            ->assertJsonCount(2);
    }

    // --- CREATION LOGIC ---

    public function test_teacher_can_create_course()
    {
        $teacher = $this->createUser(UserRole::TEACHER);
        $sourceLang = Language::factory()->create();
        $targetLang = Language::factory()->create();

        $payload = [
            'title' => 'Advanced Finnish',
            'target_language_id' => $targetLang->id,
        ];

        $this->actingAs($teacher)
            ->postJson("/api/languages/{$sourceLang->id}/courses", $payload)
            ->assertStatus(201);
    }

    public function test_student_cannot_create_course()
    {
        $student = $this->createUser(UserRole::STUDENT);
        $sourceLang = Language::factory()->create();

        $this->actingAs($student)
            ->postJson("/api/languages/{$sourceLang->id}/courses", ['title' => 'Fail Course'])
            ->assertStatus(403);
    }

    // --- OWNERSHIP & PERMISSIONS (The Critical Part) ---

    public function test_teacher_can_update_their_own_course()
    {
        $teacher = $this->createUser(UserRole::TEACHER);
        $course = Course::factory()->create(['created_by_id' => $teacher->id]);

        $this->actingAs($teacher)
            ->patchJson("/api/courses/{$course->id}", ['title' => 'Updated Title'])
            ->assertStatus(200);

        $this->assertDatabaseHas('courses', ['id' => $course->id, 'title' => 'Updated Title']);
    }

    public function test_teacher_cannot_update_another_teachers_course()
    {
        $teacherA = $this->createUser(UserRole::TEACHER);
        $teacherB = $this->createUser(UserRole::TEACHER);
        $courseOfA = Course::factory()->create(['created_by_id' => $teacherA->id]);

        $this->actingAs($teacherB)
            ->patchJson("/api/courses/{$courseOfA->id}", ['title' => 'Hacker Update'])
            ->assertStatus(403);
    }

    public function test_admin_can_update_any_teachers_course()
    {
        $admin = $this->createUser(UserRole::ADMIN);
        $teacher = $this->createUser(UserRole::TEACHER);
        $course = Course::factory()->create(['created_by_id' => $teacher->id]);

        $this->actingAs($admin)
            ->patchJson("/api/courses/{$course->id}", ['title' => 'Admin Overwrite'])
            ->assertStatus(200);
    }

    public function test_teacher_can_delete_their_own_course()
    {
        $teacher = $this->createUser(UserRole::TEACHER);
        $course = Course::factory()->create(['created_by_id' => $teacher->id]);

        $this->actingAs($teacher)
            ->deleteJson("/api/courses/{$course->id}")
            ->assertStatus(204);

        $this->assertDatabaseMissing('courses', ['id' => $course->id]);
    }

    public function test_teacher_cannot_delete_another_teachers_course()
    {
        $teacherA = $this->createUser(UserRole::TEACHER);
        $teacherB = $this->createUser(UserRole::TEACHER);
        $courseOfA = Course::factory()->create(['created_by_id' => $teacherA->id]);

        $this->actingAs($teacherB)
            ->deleteJson("/api/courses/{$courseOfA->id}")
            ->assertStatus(403);
    }

    public function test_admin_can_delete_any_course()
    {
        $admin = $this->createUser(UserRole::ADMIN);
        $course = Course::factory()->create();

        $this->actingAs($admin)
            ->deleteJson("/api/courses/{$course->id}")
            ->assertStatus(204);
    }

    // --- VALIDATION EDGE CASES ---

    public function test_course_creation_fails_with_invalid_languages()
    {
        $admin = $this->createUser(UserRole::ADMIN);

        $this->actingAs($admin)
            ->postJson('/api/languages/999/courses', [
                'title' => 'Broken Course',
                'source_language_id' => 999, // Non-existent
                'target_language_id' => 888  // Non-existent
            ])
            ->assertStatus(404);
    }

    public function test_course_creation_fails_with_invalid_target_language()
    {
        $admin = $this->createUser(UserRole::ADMIN);
        $sourceLang = Language::factory()->create();

        // UPDATED: The target_language_id is still in the body, so it stays 422
        $this->actingAs($admin)
            ->postJson("/api/languages/{$sourceLang->id}/courses", [
                'title' => 'Broken Course',
                'target_language_id' => 888 // Non-existent
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['target_language_id']);
    }
}
