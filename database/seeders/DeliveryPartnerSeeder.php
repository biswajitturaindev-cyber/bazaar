<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DeliveryPartner;

class DeliveryPartnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $partners = [
            [
                'name'         => 'Delhivery',
                'code'         => 'DELHIVERY',
                'website'      => 'https://www.delhivery.com',
                'tracking_url' => 'https://www.delhivery.com/track/package/{{tracking_id}}',
            ],
            [
                'name'         => 'Blue Dart',
                'code'         => 'BLUEDART',
                'website'      => 'https://www.bluedart.com',
                'tracking_url' => 'https://www.bluedart.com/tracking',
            ],
            [
                'name'         => 'DTDC',
                'code'         => 'DTDC',
                'website'      => 'https://www.dtdc.in',
                'tracking_url' => 'https://www.dtdc.in/tracking/tracking_results.asp',
            ],
            [
                'name'         => 'XpressBees',
                'code'         => 'XPRESSBEES',
                'website'      => 'https://www.xpressbees.com',
                'tracking_url' => 'https://www.xpressbees.com/track',
            ],
            [
                'name'         => 'Ekart',
                'code'         => 'EKART',
                'website'      => 'https://www.ekartlogistics.com',
                'tracking_url' => 'https://ekartlogistics.com/shipmenttrack',
            ],
        ];

        foreach ($partners as $partner) {

            DeliveryPartner::updateOrCreate(
                ['code' => $partner['code']],
                $partner
            );
        }
    }
}
