<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RedemptionCancelReason;

class RedemptionCancelReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reasons = [
            ['id' => 1, 'reason' => 'Ordered by mistake'],
            ['id' => 2, 'reason' => 'Redeemed wrong product'],
            ['id' => 3, 'reason' => 'Selected wrong variant'],
            ['id' => 4, 'reason' => 'Want to change reward item'],
            ['id' => 5, 'reason' => 'Changed my mind'],
            ['id' => 6, 'reason' => 'Found a better reward'],
            ['id' => 7, 'reason' => 'Delivery time is too long'],
            ['id' => 8, 'reason' => 'Shipping address issue'],
            ['id' => 9, 'reason' => 'Insufficient reward points'],
            ['id' => 10, 'reason' => 'Duplicate redemption'],
            ['id' => 11, 'reason' => 'Product no longer needed'],
            ['id' => 12, 'reason' => 'Expected a different product'],
            ['id' => 13, 'reason' => 'Accidental redemption'],
            ['id' => 14, 'reason' => 'Payment issue'],
            ['id' => 15, 'reason' => 'Technical issue during redemption'],
            ['id' => 16, 'reason' => 'Item unavailable elsewhere'],
            ['id' => 17, 'reason' => 'Want to reorder later'],
            ['id' => 18, 'reason' => 'Family/friend no longer needs it'],
            ['id' => 19, 'reason' => 'Reward value is not satisfactory'],
            ['id' => 20, 'reason' => 'Other'],
        ];

        foreach ($reasons as $item) {
            RedemptionCancelReason::updateOrCreate(
                ['id' => $item['id']],
                [
                    'reason' => $item['reason'],
                    'status' => '1',
                    'created_at' => '2026-05-11 13:02:53',
                ]
            );
        }
    }
}
