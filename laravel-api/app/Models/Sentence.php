<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $text_target The sentence written in the language being learned.
 * @property string $text_source The sentence written in the student's native language.
 * @property int $unit_id The ID of the unit this sentence belongs to.
 * @property int $user_id The ID of the user (teacher) who created this sentence.
 */
class Sentence extends Model
{
    /** @use HasFactory<\Database\Factories\SentenceFactory> */
    use HasFactory;

    protected $fillable = [
        'text_target',
        'text_source',
        'unit_id',
        'user_id'
    ];

    /**
     * The unit that contains this sentence.
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * The individual words extracted from this sentence for vocabulary practice.
     */
    public function words()
    {
        return $this->belongsToMany(Word::class);
    }
}
