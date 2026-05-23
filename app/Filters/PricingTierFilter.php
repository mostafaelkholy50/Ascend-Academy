<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class PricingTierFilter
{
    public function apply(Builder $query, Request $request): Builder
    {
        // No filters defined for PricingTier yet
        return $query;
    }
}
