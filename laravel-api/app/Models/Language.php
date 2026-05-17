<?php

namespace App\Models;

use Database\Factories\LanguageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name Full name of the language (e.g., Finnish).
 * @property string $code ISO 639-1 two-letter language code (e.g., fi).
 */
class Language extends Model
{
    /** @use HasFactory<LanguageFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'code',
    ];

    public function coursesAsSource()
    {
        return $this->hasMany(Course::class, 'source_language_id');
    }

    public function coursesAsTarget()
    {
        return $this->hasMany(Course::class, 'target_language_id');
    }
}
