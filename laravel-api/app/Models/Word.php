<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $term The word as it appears in the text.
 * @property string|null $lemma The dictionary/base form of the word (e.g., "running" -> "run").
 * @property string $translation The meaning of the word in the source language.
 */
class Word extends Model
{
    /** @use HasFactory<\Database\Factories\WordFactory> */
    use HasFactory;

    protected $fillable = [
        'term',
        'lemma',
        'translation',
    ];

    /**
     * The sentences where this specific vocabulary item is used.
     */
    public function sentences(): BelongsToMany
    {
        return $this->belongsToMany(Sentence::class);
    }
}
