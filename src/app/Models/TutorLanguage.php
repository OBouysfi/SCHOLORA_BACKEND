<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TutorLanguage extends Model
{
    use HasFactory;

    protected $fillable = [
        'tutor_id',
        'language',
        'level'
    ];

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(Tutor::class);
    }
}