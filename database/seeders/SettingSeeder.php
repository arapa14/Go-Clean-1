<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'name',
                'value' => 'Go Clean 1'
            ],
            [
                'key' => 'logo',
                'value' => 'storage/logos/GoClean1.jpg'
            ],
            [
                'key' => 'enable_time_restriction',
                'value' => '1'
            ],
            [
                'key' => 'enable_session_restriction',
                'value' => '1'
            ],
            [
                'key' => 'pagi_start',
                'value' => '06'
            ],
            [
                'key' => 'pagi_end',
                'value' => '09'
            ],
            [
                'key' => 'siang_start',
                'value' => '09'
            ],
            [
                'key' => 'siang_end',
                'value' => '13'
            ],
            [
                'key' => 'sore_start',
                'value' => '13'
            ],
            [
                'key' => 'sore_end',
                'value' => '16'
            ]
        ];    

        foreach ($settings as $setting) {
            DB::table('settings')->insert($setting);
        }
    }
}
