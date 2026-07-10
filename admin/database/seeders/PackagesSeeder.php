<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PackagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Basic',
                'stars' => 1,
                'price' => 0.00,
                'duration' => 365,
                'duration_type' => 'day',
                'product_limit' => 50,
                'description' => 'Basic package with up to 50 products.',
                'status' => 1,
            ],
            [
                'name' => 'Silver',
                'stars' => 2,
                'price' => 499.00,
                'duration' => 30,
                'duration_type' => 'day',
                'product_limit' => 200,
                'description' => 'Silver package with up to 200 products.',
                'status' => 1,
            ],
            [
                'name' => 'Gold',
                'stars' => 3,
                'price' => 999.00,
                'duration' => 30,
                'duration_type' => 'day',
                'product_limit' => 500,
                'description' => 'Gold package with up to 500 products.',
                'status' => 1,
            ],
            [
                'name' => 'Platinum',
                'stars' => 4,
                'price' => 1999.00,
                'duration' => 30,
                'duration_type' => 'day',
                'product_limit' => null,
                'description' => 'Unlimited product listing package.',
                'status' => 1,
            ],
        ];

        foreach ($packages as $package) {
            Package::updateOrCreate(
                ['name' => $package['name']],
                $package
            );
        }
    }
}
