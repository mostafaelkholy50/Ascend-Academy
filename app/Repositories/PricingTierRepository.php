<?php

namespace App\Repositories;

use App\Models\PricingTier;
use Illuminate\Database\Eloquent\Builder;

class PricingTierRepository
{
    public function getPricingTiersQuery(): Builder
    {
        return PricingTier::orderBy('days_per_week')
            ->orderBy('session_duration');
    }

    public function findOrFail(int $id): PricingTier
    {
        return PricingTier::findOrFail($id);
    }

    public function create(array $data): PricingTier
    {
        return PricingTier::create($data);
    }

    public function update(PricingTier $pricingTier, array $data): bool
    {
        return $pricingTier->update($data);
    }

    public function delete(PricingTier $pricingTier): ?bool
    {
        return $pricingTier->delete();
    }

    public function findDuplicate(int $days, int $duration, int $excludeId = null): ?PricingTier
    {
        $query = PricingTier::where('days_per_week', $days)
            ->where('session_duration', $duration);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->first();
    }
}
