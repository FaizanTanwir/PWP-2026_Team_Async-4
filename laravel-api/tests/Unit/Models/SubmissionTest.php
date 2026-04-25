<?php

namespace Tests\Unit\Models;

use App\Enums\UserRole;
use App\Http\Resources\SubmissionResource;
use App\Models\Submission;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;

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

    /**
     * Test SubmissionResource transforms data correctly.
     */
    public function test_submission_resource_formats_data_correctly(): void
    {
        $submission = Submission::factory()->create([
            'type' => 'mcq',
            'accuracy' => 1.0,
        ]);

        // FIX: Get a fresh instance from the DB to ensure no relations are auto-loaded in memory
        $submission = $submission->fresh();

        $resource = new SubmissionResource($submission);
        $data = $resource->toArray(app('request'));

        $this->assertEquals($submission->id, $data['id']);
    }

    /**
     * Test SubmissionResource includes relationships when they are loaded.
     */
    public function test_submission_resource_includes_relationships_when_loaded(): void
    {
        $submission = Submission::factory()->create();
        $submission->load(['user', 'unit']);

        $resource = new SubmissionResource($submission);
        $data = $resource->toArray(new Request());

        $this->assertArrayHasKey('user', $data);
        $this->assertArrayHasKey('unit', $data);
        $this->assertEquals($submission->user->id, $data['user']->id);
    }
}
