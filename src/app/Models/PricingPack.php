<?php
// app/Models/PricingPack.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricingPack extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'price',
        'currency',
        'billing_period',
        'virtual_classrooms',
        'sessions_per_week',
        'max_students',
        'basic_payment_collection',
        'automated_invoicing',
        'student_roster',
        'attendance_tracking',
        'full_tool_suite',
        'premium_features',
        'description',
        'features',
        'is_active',
        'is_popular',
        'sort_order',
    ];

    protected $casts = [
        'features' => 'array',
        'basic_payment_collection' => 'boolean',
        'automated_invoicing' => 'boolean',
        'student_roster' => 'boolean',
        'attendance_tracking' => 'boolean',
        'full_tool_suite' => 'boolean',
        'premium_features' => 'boolean',
        'is_active' => 'boolean',
        'is_popular' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function tutors()
    {
        return $this->hasMany(Tutor::class);
    }
}