<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class PincodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            // $path = database_path('seeders/pincodes.sql');

            // if (!File::exists($path)) {
            //     throw new \Exception('SQL file not found');
            // }

            // $sql = File::get($path);
            // DB::unprepared($sql);

    $path = database_path('seeders/pincodes.sql');

    if (!File::exists($path)) {
        dd('SQL file not found');
    }

    $sql = File::get($path);

    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    DB::unprepared($sql);
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');


        } catch (\Exception $e) {
            dd('Seeder Error: ' . $e->getMessage());
        }
    }
}
