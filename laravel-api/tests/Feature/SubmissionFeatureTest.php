<?php

namespace Tests\Feature;

use App\Models\Submission;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SubmissionFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles required for Controller logic
        Role::create(['name' => 'student']);
        Role::create(['name' => 'teacher']);
    }

    /** -----------------------------------------------------------
     * INDEX TESTS
     * ----------------------------------------------------------- */

    public function test_students_can_only_see_their_own_submissions_for_a_unit(): void
    {
        $studentA = User::factory()->create();
        $studentA->assignRole('student');

        $studentB = User::factory()->create();
        $studentB->assignRole('student');

        $unit = Unit::factory()->create();

        // Create one submission for each student
        Submission::factory()->create(['user_id' => $studentA->id, 'unit_id' => $unit->id]);
        Submission::factory()->create(['user_id' => $studentB->id, 'unit_id' => $unit->id]);

        $response = $this->actingAs($studentA)
            ->getJson("/api/units/{$unit->id}/submissions");

        $response->assertStatus(200)
            ->assertJsonCount(1); // Student A should not see Student B's results
    }

    public function test_teachers_can_see_all_student_submissions_for_a_unit(): void
    {
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');

        $unit = Unit::factory()->create();
        Submission::factory()->count(3)->create(['unit_id' => $unit->id]);

        $response = $this->actingAs($teacher)
            ->getJson("/api/units/{$unit->id}/submissions");

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    /** -----------------------------------------------------------
     * STORE TESTS (Grading & Normalization)
     * ----------------------------------------------------------- */

    public function test_grading_logic_is_case_insensitive_and_handles_whitespace(): void
    {
        $student = User::factory()->create();
        $student->assignRole('student');
        $unit = Unit::factory()->create();

        $payload = [
            'type'            => 'scramble',
            'question_text'   => 'Arrange: is, kissa, kotiin',
            'provided_answer' => '  KISSA is kotiin  ', // Mixed case + leading/trailing spaces
            'correct_answer'  => 'kissa is kotiin'
        ];

        $response = $this->actingAs($student)
            ->postJson("/api/units/{$unit->id}/submissions", $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.accuracy', 1)
            ->assertJsonPath('passed', true);
    }

    public function test_store_validation_requires_all_fields(): void
    {
        $student = User::factory()->create();
        $student->assignRole('student');
        $unit = Unit::factory()->create();

        $response = $this->actingAs($student)
            ->postJson("/api/units/{$unit->id}/submissions", []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['type', 'question_text', 'provided_answer', 'correct_answer']);
    }

    /** -----------------------------------------------------------
     * SHOW TESTS (Privacy)
     * ----------------------------------------------------------- */

    public function test_student_cannot_view_another_students_submission_detail(): void
    {
        $studentA = User::factory()->create();
        $studentA->assignRole('student');

        $studentB = User::factory()->create();
        $submission = Submission::factory()->create(['user_id' => $studentB->id]);

        $response = $this->actingAs($studentA)
            ->getJson("/api/submissions/{$submission->id}");

        $response->assertStatus(403)
            ->assertJson(['message' => 'Unauthorized']);
    }

    public function test_unauthenticated_users_cannot_access_any_submission_route(): void
    {
        $unit = Unit::factory()->create();

        // Try to hit the index without actingAs(user)
        $this->getJson("/api/units/{$unit->id}/submissions")
            ->assertStatus(401);
    }
}
