<?php

namespace App\Services;

use App\Repositories\PricingTierRepository;
use App\Filters\PricingTierFilter;
use App\Models\PricingTier;
use Illuminate\Http\Request;

class PricingTierService
{
    protected $repository;
    protected $filter;

    public function __construct(PricingTierRepository $repository, PricingTierFilter $filter)
    {
        $this->repository = $repository;
        $this->filter = $filter;
    }

    public function getIndexData(Request $request)
    {
        $query = $this->repository->getPricingTiersQuery();
        $query = $this->filter->apply($query, $request);

        return $query->get();
    }

    public function storePricingTier(array $data)
    {
        $existing = $this->repository->findDuplicate(
            $data['days_per_week'],
            $data['session_duration']
        );

        if ($existing) {
            throw new \Exception('A pricing tier with this configuration already exists.');
        }

        $data['is_active'] = $data['is_active'] ?? true;

        return $this->repository->create($data);
    }

    public function updatePricingTier(PricingTier $pricingTier, array $data)
    {
        $existing = $this->repository->findDuplicate(
            $data['days_per_week'],
            $data['session_duration'],
            $pricingTier->id
        );

        if ($existing) {
            throw new \Exception('A pricing tier with this configuration already exists.');
        }

        $data['is_active'] = $data['is_active'] ?? true;

        return $this->repository->update($pricingTier, $data);
    }

    public function deletePricingTier(PricingTier $pricingTier)
    {
        return $this->repository->delete($pricingTier);
    }
}
