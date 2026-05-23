<?php

namespace App\Http\Controllers\Admin;

use App\Models\PricingTier;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\PricingTierService;
use App\Http\Requests\Admin\StorePricingTierRequest;
use App\Http\Requests\Admin\UpdatePricingTierRequest;

class PricingTierController extends Controller
{
    protected $pricingTierService;

    public function __construct(PricingTierService $pricingTierService)
    {
        $this->pricingTierService = $pricingTierService;
    }

    public function index(Request $request)
    {
        $pricingTiers = $this->pricingTierService->getIndexData($request);
        return view('admin.pricing-tiers.index', compact('pricingTiers'));
    }

    public function create()
    {
        return view('admin.pricing-tiers.create');
    }

    public function store(StorePricingTierRequest $request)
    {
        try {
            $this->pricingTierService->storePricingTier($request->validated());

            return redirect()->route('admin.pricing-tiers.index')
                ->with('success', 'Pricing tier created successfully!');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
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

    public function update(UpdatePricingTierRequest $request, PricingTier $pricingTier)
    {
        try {
            $this->pricingTierService->updatePricingTier($pricingTier, $request->validated());

            return back()->with('success', 'Pricing tier updated successfully.');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy(PricingTier $pricingTier)
    {
        $this->pricingTierService->deletePricingTier($pricingTier);
        return redirect()->route('admin.pricing-tiers.index')
            ->with('success', 'Pricing tier deleted successfully.');
    }
}
