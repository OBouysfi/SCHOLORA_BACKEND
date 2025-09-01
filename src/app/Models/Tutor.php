<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tutor extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name', 
        'email',
        'country',
        'main_subject',
        'phone',
        'is_over_18',
        'profile_photo',
        'description',
        'intro_video',
        'video_link',
        'video_thumbnail',
        'hourly_rate',
        'currency',
        'status',
        'submitted_at',
        'approved_at',
        'rejection_reason',
        'total_hours',
        'average_rating',
        'total_reviews'
    ];

    protected $casts = [
        'is_over_18' => 'boolean',
        'hourly_rate' => 'decimal:2',
        'average_rating' => 'decimal:2',
        'total_hours' => 'integer',
        'total_reviews' => 'integer',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime'
    ];

    // Relationships
    public function languages(): HasMany
    {
        return $this->hasMany(TutorLanguage::class);
    }

    public function certifications(): HasMany
    {
        return $this->hasMany(TutorCertification::class);
    }

    public function education(): HasMany
    {
        return $this->hasMany(TutorEducation::class);
    }

    public function availability(): HasMany
    {
        return $this->hasMany(TutorAvailability::class);
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(TutorSubject::class);
    }

    public function registrationSteps(): HasMany
    {
        return $this->hasMany(TutorRegistrationStep::class);
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getProfilePhotoUrlAttribute(): ?string
    {
        return $this->profile_photo ? asset('storage/' . $this->profile_photo) : null;
    }

    public function getIntroVideoUrlAttribute(): ?string
    {
        return $this->intro_video ? asset('storage/' . $this->intro_video) : null;
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeBySubject($query, $subject)
    {
        return $query->whereHas('subjects', function ($q) use ($subject) {
            $q->where('subject', $subject);
        });
    }

    // Methods
    public function isProfileComplete(): bool
    {
        return $this->registrationSteps()->where('status', 'complete')->count() >= 8;
    }

    public function getCompletionPercentage(): int
    {
        $completedSteps = $this->registrationSteps()->where('status', 'complete')->count();
        return round(($completedSteps / 8) * 100);
    }

    public function canSubmitForApproval(): bool
    {
        return $this->isProfileComplete() && $this->status === 'draft';
    }
}