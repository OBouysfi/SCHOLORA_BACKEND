<?php
// app/Http/Controllers/Api/Admin/PricingPackController.php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\PricingPack;
use Illuminate\Http\Request;

class PricingPackController extends Controller
{
    public function index()
    {
        $packs = PricingPack::orderBy('sort_order')->get();
        return response()->json($packs);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'slug' => 'required|string|unique:pricing_packs',
            'price' => 'required|numeric',
            'currency' => 'string',
            'billing_period' => 'in:monthly,annual',
            'virtual_classrooms' => 'integer',
            'sessions_per_week' => 'nullable|integer',
            'max_students' => 'integer',
            'description' => 'nullable|string',
            'basic_payment_collection' => 'boolean',
            'automated_invoicing' => 'boolean',
            'student_roster' => 'boolean',
            'attendance_tracking' => 'boolean',
            'full_tool_suite' => 'boolean',
            'premium_features' => 'boolean',
            'is_active' => 'boolean',
            'is_popular' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $pack = PricingPack::create($validated);
        return response()->json($pack, 201);
    }

    public function update(Request $request, $id)
    {
        try {
            $pack = PricingPack::findOrFail($id);
            
            // Pas de validation stricte pour l'update
            $pack->update($request->all());
            
            return response()->json($pack);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            PricingPack::findOrFail($id)->delete();
            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}