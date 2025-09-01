<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TutorEducation extends Model
{
    use HasFactory;

    protected $table = 'tutor_education';

    protected $fillable = [
        'tutor_id',
        'university',
        'degree',
        'degree_type',
        'specialization',
        'year_from',
        'year_to',
        'diploma_file',
        'verification_status'
    ];

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(Tutor::class);
    }

    public function getDiplomaFileUrlAttribute(): ?string
    {
        return $this->diploma_file ? asset('storage/' . $this->diploma_file) : null;
    }

    public function getStudyPeriodAttribute(): string
    {
        return $this->year_from . ' - ' . $this->year_to;
    }
}