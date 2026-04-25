<?php

namespace Tests\Unit\Models;

use App\Enums\UserRole;
use App\Models\Course;
use App\Models\Language;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CourseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create the roles in the test database
        Role::create(['name' => UserRole::TEACHER->value]);
        Role::create(['name' => UserRole::STUDENT->value]);
    }

    /**
     * Test that a course has a source language relationship.
     */
    public function test_course_belongs_to_a_source_language(): void
    {
        $language = Language::factory()->create(['name' => 'English']);
        $course = Course::factory()->create(['source_language_id' => $language->id]);

        $this->assertInstanceOf(Language::class, $course->sourceLanguage);
        $this->assertEquals('English', $course->sourceLanguage->name);
    }

    /**
     * Test that a course has a target language relationship.
     */
    public function test_course_belongs_to_a_target_language(): void
    {
        $language = Language::factory()->create(['name' => 'Finnish']);
        $course = Course::factory()->create(['target_language_id' => $language->id]);

        $this->assertInstanceOf(Language::class, $course->targetLanguage);
        $this->assertEquals('Finnish', $course->targetLanguage->name);
    }

    /**
     * Test that a course belongs to a teacher (creator).
     */
    public function test_course_belongs_to_a_teacher(): void
    {
        $teacher = User::factory()->create(['name' => 'Mahtab Alam']);
        $course = Course::factory()->create(['created_by_id' => $teacher->id]);

        $this->assertInstanceOf(User::class, $course->teacher);
        $this->assertEquals('Mahtab Alam', $course->teacher->name);
    }

    /**
     * Test that a course can have multiple units.
     */
    public function test_course_has_many_units(): void
    {
        $course = Course::factory()->create();
        Unit::factory()->count(3)->create(['course_id' => $course->id]);

        $this->assertCount(3, $course->units);
        $this->assertInstanceOf(Unit::class, $course->units->first());
    }

    /**
     * Test mass assignment protection.
     */
    public function test_course_fillable_attributes(): void
    {
        $fillable = ['title', 'source_language_id', 'target_language_id', 'created_by_id'];
        $model = new Course();

        $this->assertEquals($fillable, $model->getFillable());
    }
}
