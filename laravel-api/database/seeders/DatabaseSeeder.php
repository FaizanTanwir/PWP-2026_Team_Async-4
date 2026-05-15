<?php

namespace Database\Seeders;

use App\Enums\UserRole;
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
            [
                'target' => 'Hyvää huomenta',
                'source' => 'Good morning',
                'word_details' => [
                    ['term' => 'Hyvää', 'translation' => 'Good', 'lemma' => 'hyvä'],
                    ['term' => 'huomenta', 'translation' => 'morning', 'lemma' => 'huomen'],
                ]
            ],
            [
                'target' => 'Mitä kuuluu?',
                'source' => 'How are you?',
                'word_details' => [
                    ['term' => 'Mitä', 'translation' => 'What', 'lemma' => 'mikä'],
                    ['term' => 'kuuluu', 'translation' => 'belongs/is heard', 'lemma' => 'kuulua'],
                ]
            ],
            [
                'target' => 'Kiitos paljon',
                'source' => 'Thank you very much',
                'word_details' => [
                    ['term' => 'Kiitos', 'translation' => 'Thank you', 'lemma' => 'kiitos'],
                    ['term' => 'paljon', 'translation' => 'much', 'lemma' => 'paljon'],
                ]
            ],
            [
                'target' => 'Hauska tavata',
                'source' => 'Nice to meet you',
                'word_details' => [
                    ['term' => 'Hauska', 'translation' => 'Nice/Fun', 'lemma' => 'hauska'],
                    ['term' => 'tavata', 'translation' => 'to meet', 'lemma' => 'tavata'],
                ]
            ],
        ];

        foreach ($data as $item) {
            // 1. Create the Sentence
            $sentence = Sentence::create([
                'text_target' => $item['target'],
                'text_source' => $item['source'],
                'unit_id' => $unit1->id,
                'user_id' => $course->created_by_id,
            ]);

            foreach ($item['word_details'] as $details) {
                // 2. Use firstOrCreate with 'term' but update/fill other fields
                $word = Word::firstOrCreate(
                    ['term' => $details['term']], // Unique check
                    [
                        'translation' => $details['translation'],
                        'lemma' => $details['lemma'],
                        'language_id' => $course->target_language_id // Ensure words belong to the course language
                    ]
                );

                // 3. Attach to the sentence (Pivot table)
                $sentence->words()->syncWithoutDetaching([$word->id]);
            }
        }

        $this->call([
            SubmissionSeeder::class
        ]);

    }
}
