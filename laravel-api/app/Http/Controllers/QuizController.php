<?php

namespace App\Http\Controllers;

use App\Enums\QuestionType;
use App\Models\Unit;
use App\Models\Word;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    public function generate(Unit $unit)
    {
        // 1. Get more sentences than we need to ensure variety
        $sentences = $unit->sentences()->with('words')->get();

        if ($sentences->count() < 3) {
            return response()->json(['message' => 'Add more sentences to this unit to generate a quiz.'], 422);
        }

        // 2. Shuffle and loop to create 10 unique-ish items
        $questions = collect();
        for ($i = 0; $i < 10; $i++) {
            $sentence = $sentences->random();
            // Pick a random type from your actual Enum
            $type = collect(QuestionType::cases())->random();
            $questions->push($this->formatQuestion($sentence, $type));
        }

        return response()->json([
            'unit_id' => $unit->id,
            'questions' => $questions
        ]);
    }

    private function formatQuestion($sentence, QuestionType $type)
    {
        return match ($type) {
            QuestionType::SCRAMBLE => $this->generateScramble($sentence),
            QuestionType::MCQ => $this->generateMcq($sentence),
            QuestionType::TRANSLATION => [
                'type' => 'translation',
                'question_text' => "Translate into target language: '{$sentence->text_source}'",
                'correct_answer' => $sentence->text_target,
            ],
            // Map your specific enum cases here instead of using 'default'
            QuestionType::FILL_IN_THE_BLANK => $this->generateFillInBlank($sentence),
            QuestionType::WORD_MEANING => $this->generateWordMeaning($sentence),
        };
    }

    private function generateScramble($sentence)
    {
        $text = $sentence->text_target;
        $words = explode(' ', $text);

        // Ensure it actually scrambles for short sentences
        if (count($words) > 1) {
            $original = implode(' ', $words);
            while (implode(' ', $words) === $original) {
                shuffle($words);
            }
        }

        return [
            'type' => 'scramble',
            'question_text' => implode(' ', $words),
            'correct_answer' => $text,
        ];
    }

    private function generateMcq($sentence)
    {
        // 1. Pick a random word from the sentence
        $word = $sentence->words->random();

        // 2. Try to get distractors from the SAME UNIT first for a better challenge
        $unitWordIds = DB::table('sentence_word')
            ->whereIn('sentence_id', $sentence->unit->sentences->pluck('id'))
            ->pluck('word_id')
            ->unique();

        $distractors = Word::whereIn('id', $unitWordIds)
            ->where('id', '!=', $word->id)
            ->inRandomOrder()
            ->limit(3)
            ->pluck('translation')
            ->toArray();

        // 3. Fallback: If the unit is tiny, grab random words from the same language
        if (count($distractors) < 3) {
            $extraDistractors = Word::where('language_id', $sentence->unit->course->target_language_id)
                ->where('id', '!=', $word->id)
                ->whereNotIn('translation', $distractors) // Don't duplicate
                ->inRandomOrder()
                ->limit(3 - count($distractors))
                ->pluck('translation')
                ->toArray();

            $distractors = array_merge($distractors, $extraDistractors);
        }

        $options = array_merge([$word->translation], $distractors);
        shuffle($options);

        return [
            'type' => 'mcq',
            'question_text' => "What does '{$word->term}' mean?",
            'correct_answer' => $word->translation,
            'options' => $options
        ];
    }

    private function generateFillInBlank($sentence)
    {
        $words = explode(' ', $sentence->text_target);
        $index = array_rand($words);
        $removed = $words[$index];
        $words[$index] = '____';

        return [
            'type' => 'fill_in_the_blank',
            'question_text' => "Complete the sentence: " . implode(' ', $words),
            'correct_answer' => $removed,
        ];
    }

    private function generateWordMeaning($sentence)
    {
        $word = $sentence->words->random();
        return [
            'type' => 'word_meaning',
            'question_text' => "What is the Finnish term for '{$word->translation}'?",
            'correct_answer' => $word->term,
        ];
    }
}
