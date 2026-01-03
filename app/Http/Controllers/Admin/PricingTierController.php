<?php

namespace App\Http\Controllers\Admin;

use App\Models\PricingTier;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PricingTierController extends Controller
{
    public function index()
    {
        $pricingTiers = PricingTier::orderBy('days_per_week')
            ->orderBy('session_duration')
            ->get();

        return view('admin.pricing-tiers.index', compact('pricingTiers'));
    }

    public function create()
    {
        return view('admin.pricing-tiers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'days_per_week' => 'required|integer|min:1|max:7',
            'session_duration' => 'required|in:30,60',
            'price_cad' => 'required|numeric|min:0',
            'price_usd' => 'required|numeric|min:0',
            'price_gbp' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            // Check for duplicate
            $existing = PricingTier::where('days_per_week', $request->days_per_week)
                ->where('session_duration', $request->session_duration)
                ->first();

            if ($existing) {
                return back()->with('error', 'A pricing tier with this configuration already exists.');
            }

            PricingTier::create([
                'days_per_week' => $request->days_per_week,
                'session_duration' => $request->session_duration,
                'price_cad' => $request->price_cad,
                'price_usd' => $request->price_usd,
                'price_gbp' => $request->price_gbp,
                'is_active' => true, // Always active
                'notes' => $request->notes,
            ]);

            return redirect()->route('admin.pricing-tiers.index')
                ->with('success', 'Pricing tier created successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create pricing tier: ' . $e->getMessage());
        }
    }

    public function show(PricingTier $pricingTier)
    {
        return view('admin.pricing-tiers.show', compact('pricingTier'));
    }

    public function edit(PricingTier $pricingTier)
    {
        return view('admin.pricing-tiers.edit', compact('pricingTier'));
    }

    public function update(Request $request, PricingTier $pricingTier)
    {
        $request->validate([
            'days_per_week' => 'required|integer|min:1|max:7',
            'session_duration' => 'required|in:30,60',
            'price_cad' => 'required|numeric|min:0',
            'price_usd' => 'required|numeric|min:0',
            'price_gbp' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'notes' => 'nullable|string|max:500',
        ]);

        // Check for duplicate (excluding current record)
        $existing = PricingTier::where('days_per_week', $request->days_per_week)
            ->where('session_duration', $request->session_duration)
            ->where('id', '!=', $pricingTier->id)
            ->first();

        if ($existing) {
            return back()->with('error', 'A pricing tier with this configuration already exists.');
        }

        $pricingTier->update([
            'days_per_week' => $request->days_per_week,
            'session_duration' => $request->session_duration,
            'price_cad' => $request->price_cad,
            'price_usd' => $request->price_usd,
            'price_gbp' => $request->price_gbp,
            'is_active' => true, // Always active
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'Pricing tier updated successfully.');
    }

    public function destroy(PricingTier $pricingTier)
    {
        $pricingTier->delete();
        return redirect()->route('admin.pricing-tiers.index')
            ->with('success', 'Pricing tier deleted successfully.');
    }
}
