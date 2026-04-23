<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attempt extends Model
{
    /** @use HasFactory<\Database\Factories\AttemptFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'audio_url',
        'score',
        'sentence_id',
        'user_id',
    ];
}
