<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Package;
use App\Models\UserSubscription;

use Carbon\Carbon;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'ishitagiri@gmail.com'],
            [
                'vendor_id' => 'RV00015838',
                'name' => 'Ishita Giri',
                'password' => Hash::make('123456'),
                'mobile' => '9876543210',
                'dob' => '1995-05-05',
                'gender' => 2,
                'wallet1' => 0,
                'wallet2' => 0,
                'wallet3' => 0,
                'status' => 1,
                'kyc_status' => 0,
                'profile_status' => 1,
                'email_verified_at' => now(),
            ]
        );
        // Get Basic Package
        $package = Package::where('name', 'Basic')->first();

        if ($package) {
            $startDate = Carbon::today();

            $endDate = match ($package->duration_type) {
                'day'   => $startDate->copy()->addDays($package->duration),
                'month' => $startDate->copy()->addMonths($package->duration),
                'year'  => $startDate->copy()->addYears($package->duration),
            };

            UserSubscription::updateOrCreate(
                [
                    'user_id'    => $user->id,
                    'package_id' => $package->id,
                ],
                [
                    'amount'           => $package->price,
                    'start_date'       => $startDate,
                    'end_date'         => $endDate,
                    'payment_status'   => 'paid',
                    'payment_method'   => '',
                    'transaction_id'   => null,
                    'status'           => 1,
                ]
            );
        }


    }
}
