<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            [
                'location' => 'Dalam Ruangan'
            ],
            [
                'location' => 'Luar Ruangan'
            ],
            [
                'location' => 'Ruang Bengkel'
            ],
            [
                'location' => 'Toilet'
            ],
            [
                'location' => 'Ruang Theater'
            ],
            [
                'location' => 'Ruang Theater'
            ],
            [
                'location' => 'Ruang Serba Guna'
            ],
            [
                'location' => 'Aula Atas Lantai 2'
            ],
            [
                'location' => 'Ruang TU'
            ],
            [
                'location' => 'Ruang LSP'
            ],
        ];
        
            foreach ($locations as $location) {
                DB::table('locations')->insert($location);
            }
    }
}
