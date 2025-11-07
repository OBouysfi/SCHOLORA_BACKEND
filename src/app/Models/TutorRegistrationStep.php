<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TutorRegistrationStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'tutor_id',
        'step_name',
        'status',
        'data',
        'completed_at'
    ];

    protected $casts = [
        'data' => 'array',
        'completed_at' => 'datetime'
    ];

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(Tutor::class);
    }

    public function scopeComplete($query)
    {
        return $query->where('status', 'complete');
    }

    public function scopeForStep($query, $stepName)
    {
        return $query->where('step_name', $stepName);
    }

    public function markAsComplete()
    {
        $this->update([
            'status' => 'complete',
            'completed_at' => now()
        ]);
    }
}
