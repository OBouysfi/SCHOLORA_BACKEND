<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TutorCertification extends Model
{
    use HasFactory;

    protected $fillable = [
        'tutor_id',
        'subject',
        'certification_name',
        'is_custom_certification',
        'custom_certification_name',
        'year_from',
        'year_to',
        'certificate_file',
        'verification_status'
    ];

    protected $casts = [
        'is_custom_certification' => 'boolean'
    ];

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(Tutor::class);
    }

    public function getCertificateFileUrlAttribute(): ?string
    {
        return $this->certificate_file ? asset('storage/' . $this->certificate_file) : null;
    }

    public function getCertificationDisplayNameAttribute(): string
    {
        return $this->is_custom_certification ? 
            $this->custom_certification_name : 
            $this->certification_name;
    }
}