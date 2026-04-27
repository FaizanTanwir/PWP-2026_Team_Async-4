<?php

namespace App\Jobs;

use App\Models\Unit;
use App\Models\User;
use App\Models\Word;
use App\Services\TranslationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessUnitFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Unit $unit,
        public User $user,
        public string $content
    ) {}

    public function handle(TranslationService $translator): void
    {
        $course = $this->unit->course;
        $targetLang = $course->targetLanguage->code;
        $sourceLang = $course->sourceLanguage->code;
        $targetLangId = $course->target_language_id;

        // Split file content into sentences (handling new lines)
        $lines = collect(explode("\n", $this->content))
            ->map(fn($line) => trim($line))
            ->filter();

        foreach ($lines as $line) {
            // 1. Translate Sentence
            $translation = $translator->translate($line, $sourceLang, $targetLang);

            $sentence = $this->unit->sentences()->create([
                'text_target' => $line,
                'text_source' => $translation ?? '',
                'user_id'     => $this->user->id,
            ]);

            // 2. Tokenize and Translate Words
            $terms = $translator->tokenize($line);
            $wordIds = [];

            foreach ($terms as $term) {
                $wordTranslation = $translator->translate($term, $sourceLang, $targetLang);

                $word = Word::updateOrCreate(
                    ['term' => $term],
                    [
                        'translation' => $wordTranslation,
                        'language_id' => $targetLangId
                    ]
                );
                $wordIds[] = $word->id;
            }

            $sentence->words()->sync($wordIds);
        }
    }
}
