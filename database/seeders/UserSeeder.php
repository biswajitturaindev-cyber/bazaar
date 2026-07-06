<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
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
    }
}
