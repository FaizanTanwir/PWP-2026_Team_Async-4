<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Attempt;
use App\Models\Course;
use App\Models\Language;
use App\Models\Sentence;
use App\Models\Unit;
use App\Models\User;
use App\Models\Word;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
        ]);

        // Seed Languages
        $en = Language::create(['name' => 'English', 'code' => 'en']);
        $fi = Language::create(['name' => 'Finnish', 'code' => 'fi']);

        // Seed Course
        $course = Course::create([
            'title' => 'Finnish for Beginners',
            'source_language_id' => $en->id,
            'target_language_id' => $fi->id,
            'created_by_id' => User::role(UserRole::TEACHER->value)->get()->random()->id,
        ]);

        // Seed Unit
        $unit1 = Unit::create([
            'title' => 'Greetings & Basics',
            'course_id' => $course->id,
        ]);

        // Seed Sentences & Words
        $data = [
            ['target' => 'Hyvää huomenta', 'source' => 'Good morning', 'words' => ['Hyvää', 'huomenta']],
            ['target' => 'Mitä kuuluu?', 'source' => 'How are you?', 'words' => ['Mitä', 'kuuluu']],
            ['target' => 'Kiitos paljon', 'source' => 'Thank you very much', 'words' => ['Kiitos', 'paljon']],
            ['target' => 'Hauska tavata', 'source' => 'Nice to meet you', 'words' => ['Hauska', 'tavata']],
        ];

        foreach ($data as $item) {
            $sentence = Sentence::create([
                'text_target' => $item['target'],
                'text_source' => $item['source'],
                'unit_id' => $unit1->id,
            ]);

            foreach ($item['words'] as $term) {
                // firstOrCreate handles the "if (!word) { save }" logic from NestJS
                $word = Word::firstOrCreate(['term' => $term]);

                // This handles the sentence_words pivot table automatically
                $sentence->words()->attach($word->id);
            }
        }

        // Seed attempts
        Attempt::factory()->create(['user_id' => 1, 'sentence_id' => 1]);
    }
}
