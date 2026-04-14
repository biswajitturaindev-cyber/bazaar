<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            $path = database_path('seeders/cities.sql');

            if (!File::exists($path)) {
                throw new \Exception('SQL file not found');
            }

            $sql = File::get($path);
            DB::unprepared($sql);

        } catch (\Exception $e) {
            dd('Seeder Error: ' . $e->getMessage());
        }
    }
}
