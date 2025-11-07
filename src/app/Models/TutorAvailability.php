<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TutorAvailability extends Model
{
    use HasFactory;

    protected $table = 'tutor_availability';

    protected $fillable = [
        'tutor_id',
        'timezone',
        'day_of_week',
        'start_time',
        'end_time'
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i'
    ];

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(Tutor::class);
    }

    public function scopeForDay($query, $day)
    {
        return $query->where('day_of_week', strtolower($day));
    }

    public function getTimeSlotAttribute(): string
    {
        return $this->start_time->format('H:i') . ' - ' . $this->end_time->format('H:i');
    }
}