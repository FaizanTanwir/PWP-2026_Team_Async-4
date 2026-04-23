<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sentence extends Model
{
    /** @use HasFactory<\Database\Factories\SentenceFactory> */
    use HasFactory;

    protected $fillable = [
        'text_target',
        'text_source',
        'unit_id'
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
    public function attempts()
    {
        return $this->hasMany(Attempt::class);
    }
    public function words()
    {
        return $this->belongsToMany(Word::class);
    }
}
