<?php

namespace Tests\Unit\Models;

use App\Enums\UserRole;
use App\Models\Language;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LanguageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create the roles in the test database
        Role::create(['name' => UserRole::TEACHER->value]);
        Role::create(['name' => UserRole::STUDENT->value]);
    }

    public function test_language_can_be_created_with_fillable_attributes(): void
    {
        $data = ['name' => 'Urdu', 'code' => 'ur'];
        $language = Language::create($data);

        $this->assertDatabaseHas('languages', $data);
        $this->assertEquals('Urdu', $language->name);
    }

    public function test_language_has_timestamps_disabled(): void
    {
        $language = Language::factory()->create();

        $this->assertArrayNotHasKey('created_at', $language->toArray());
        $this->assertFalse($language->usesTimestamps());
    }
}
