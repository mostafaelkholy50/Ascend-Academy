<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PricingTier;

class PricingTierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tiers = [
            [
                'days_per_week' => 1,
                'session_duration' => '30',
                'price_cad' => 50.00,
                'price_usd' => 40.00,
                'price_gbp' => 30.00,
                'is_active' => true,
            ],
            [
                'days_per_week' => 2,
                'session_duration' => '30',
                'price_cad' => 90.00,
                'price_usd' => 70.00,
                'price_gbp' => 55.00,
                'is_active' => true,
            ],
            [
                'days_per_week' => 1,
                'session_duration' => '60',
                'price_cad' => 100.00,
                'price_usd' => 80.00,
                'price_gbp' => 60.00,
                'is_active' => true,
            ],
            [
                'days_per_week' => 2,
                'session_duration' => '60',
                'price_cad' => 180.00,
                'price_usd' => 140.00,
                'price_gbp' => 110.00,
                'is_active' => true,
            ],
        ];

        foreach ($tiers as $tierData) {
            PricingTier::firstOrCreate(
                [
                    'days_per_week' => $tierData['days_per_week'],
                    'session_duration' => $tierData['session_duration']
                ],
                $tierData
            );
        }
    }
}
