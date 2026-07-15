<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\AdminSeeder;
use Database\Seeders\HsnSeeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call([
            AdminSeeder::class,
            PackagesSeeder::class,
            HsnSeeder::class,
            BusinessCategorySeeder::class,
            CategorySeeder::class,
            //SubCategorySeeder::class,
            AttributeMasterSeeder::class,
            //AttributeSeeder::class,
            //AttributeValueSeeder::class,
            StateSeeder::class,
            CitySeeder::class,
            //PincodeSeeder::class,
            DeliveryPartnerSeeder::class,
            RedemptionCancelReasonSeeder::class,
            //UserSeeder::class,
            BusinessSeeder::class,
            BusinessAddressSeeder::class,
            BusinessAgreementSeeder::class,
            BusinessContactSeeder::class,
            ]);



    }
}
