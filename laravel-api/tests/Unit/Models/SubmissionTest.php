<?php

namespace Tests\Unit\Models;

use App\Enums\UserRole;
use App\Models\Submission;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class SubmissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create the roles in the test database
        Role::create(['name' => UserRole::TEACHER->value]);
        Role::create(['name' => UserRole::STUDENT->value]);
    }

    public function test_submission_belongs_to_a_user_and_unit(): void
    {
        // This triggers the Laravel container, so it needs Tests\TestCase
        $user = User::factory()->create();
        $unit = Unit::factory()->create();
        $submission = Submission::factory()->create([
            'user_id' => $user->id,
            'unit_id' => $unit->id
        ]);

        $this->assertInstanceOf(User::class, $submission->user);
        $this->assertInstanceOf(Unit::class, $submission->unit);
    }

    public function test_is_passed_attribute_logic(): void
    {
        // Even 'make' triggers casting logic which needs the 'config' class
        $passedSubmission = Submission::factory()->make(['accuracy' => 0.7]);
        $failedSubmission = Submission::factory()->make(['accuracy' => 0.69]);

        $this->assertTrue($passedSubmission->is_passed);
        $this->assertFalse($failedSubmission->is_passed);
    }
}
