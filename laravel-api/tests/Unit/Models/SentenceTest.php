<?php

namespace Tests\Unit\Models;

use App\Enums\UserRole;
use App\Models\Sentence;
use App\Models\Unit;
use App\Models\Word;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SentenceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create the roles in the test database
        Role::create(['name' => UserRole::TEACHER->value]);
        Role::create(['name' => UserRole::STUDENT->value]);
    }

    public function test_sentence_belongs_to_a_unit(): void
    {
        $unit = Unit::factory()->create();
        $sentence = Sentence::factory()->create(['unit_id' => $unit->id]);

        $this->assertInstanceOf(Unit::class, $sentence->unit);
        $this->assertEquals($unit->id, $sentence->unit->id);
    }

    public function test_sentence_belongs_to_many_words(): void
    {
        $sentence = Sentence::factory()->create();
        $words = Word::factory()->count(3)->create();

        $sentence->words()->attach($words->pluck('id'));

        $this->assertCount(3, $sentence->words);
        $this->assertInstanceOf(Word::class, $sentence->words->first());
    }
}
