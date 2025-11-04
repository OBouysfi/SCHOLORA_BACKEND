<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getProfilePhotoUrlAttribute(): ?string
    {
        return $this->profile_photo ? asset('storage/' . $this->profile_photo) : null;
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}