<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name Full name of the language (e.g., Finnish).
 * @property string $code ISO 639-1 two-letter language code (e.g., fi).
 */
class Language extends Model
{
    /** @use HasFactory<\Database\Factories\LanguageFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'code',
    ];
}
