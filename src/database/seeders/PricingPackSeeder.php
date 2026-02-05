<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PricingPackSeeder extends Seeder
{
    public function run()
    {
        DB::table('pricing_packs')->insert([
            [
                'name' => 'Free Pack',
                'slug' => 'free',
                'price' => 0,
                'currency' => 'MAD',
                'billing_period' => 'monthly',
                'virtual_classrooms' => 1,
                'sessions_per_week' => 2,
                'max_students' => 10,
                'basic_payment_collection' => true,
                'automated_invoicing' => false,
                'student_roster' => true,
                'attendance_tracking' => true,
                'full_tool_suite' => false,
                'premium_features' => false,
                'description' => 'Parfait pour les tuteurs individuels qui démarrent',
                'features' => json_encode([
                    '1 salle virtuelle',
                    '2 sessions par semaine',
                    "Jusqu'à 10 étudiants",
                    'Collecte de paiements basique',
                    'Gestion des présences'
                ]),
                'is_active' => true,
                'is_popular' => false,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Professional Pack',
                'slug' => 'professional',
                'price' => 319,
                'currency' => 'MAD',
                'billing_period' => 'monthly',
                'virtual_classrooms' => 3,
                'sessions_per_week' => null,
                'max_students' => 50,
                'basic_payment_collection' => true,
                'automated_invoicing' => true,
                'student_roster' => true,
                'attendance_tracking' => true,
                'full_tool_suite' => true,
                'premium_features' => true,
                'description' => 'Fonctionnalités avancées pour éducateurs établis',
                'features' => json_encode([
                    '3 salles virtuelles',
                    'Sessions illimitées par semaine',
                    "Jusqu'à 50 étudiants",
                    'Facturation et paiements automatisés',
                    'Suite complète d\'outils et fonctionnalités premium'
                ]),
                'is_active' => true,
                'is_popular' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}