<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Word extends Model
{
    /** @use HasFactory<\Database\Factories\WordFactory> */
    use HasFactory;

    protected $fillable = [
        'term',
        'lemma',
        'translation',
    ];

    public function sentences(): BelongsToMany
    {
        return $this->belongsToMany(Sentence::class);
    }
}
