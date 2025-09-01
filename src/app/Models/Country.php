<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'flag_emoji',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function tutors(): HasMany
    {
        return $this->hasMany(Tutor::class, 'country', 'name');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getRouteKeyName()
    {
        return 'code';
    }
}
