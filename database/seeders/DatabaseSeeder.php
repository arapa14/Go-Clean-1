<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = [
            // Data Dummy
            [
                'name'     => 'Admin',
                'email'    => 'admin@gmail.com',
                'role'     => 'admin',
                'password' => Hash::make('lorem-ipsum'),
            ],
            [
                'name'     => 'Reviewer',
                'email'    => 'reviewer@gmail.com',
                'role'     => 'reviewer',
                'password' => Hash::make('lorem-ipsum'),
            ],
            [
                'name'     => 'Caraka',
                'email'    => 'caraka@gmail.com',
                'role'     => 'petugas-kebersihan',
                'password' => Hash::make('123123123'),
            ],

            // // Data Caraka - petugas-kebersihan
            [
                'name'     => 'Ari Rusdiana',
                'email'    => 'arirusdiana92@gmail.com',
                'role'     => 'petugas-kebersihan',
                'password' => Hash::make('123123123'),
            ],
            [
                'name'     => 'Suhendi',
                'email'    => 'suhend807@gmail.com',
                'role'     => 'petugas-kebersihan',
                'password' => Hash::make('123123123'),
            ],
            [
                'name'     => 'Herry Kurniawan',
                'email'    => 'herry19933@gmail.com',
                'role'     => 'petugas-kebersihan',
                'password' => Hash::make('123123123'),
            ],
            [
                'name'     => 'Devian Sukma Setiawan',
                'email'    => 'devianalrescha63@gmail.com',
                'role'     => 'petugas-kebersihan',
                'password' => Hash::make('123123123'),
            ],
            [
                'name'     => 'Hermanudin',
                'email'    => 'herman251283@gmail.com',
                'role'     => 'petugas-kebersihan',
                'password' => Hash::make('123123123'),
            ],
            [
                'name'     => 'Fauji Mutaqin',
                'email'    => 'ubaylulu20@gmail.com',
                'role'     => 'petugas-kebersihan',
                'password' => Hash::make('123123123'),
            ],
            [
                'name'     => 'Darojatun Agung',
                'email'    => 'dragtrisan@gmail.com',
                'role'     => 'petugas-kebersihan',
                'password' => Hash::make('123123123'),
            ],
            [
                'name'     => 'Muhamad Ihsanto',
                'email'    => 'iksanxme@gmail.com',
                'role'     => 'petugas-kebersihan',
                'password' => Hash::make('123123123'),
            ],
            [
                'name'     => 'ENDANG',
                'email'    => 'yeyetendang1@gmail.com',
                'role'     => 'petugas-kebersihan',
                'password' => Hash::make('123123123'),
            ],

            // Data Toolmen = juru-bengkel
            [
                'name'     => 'Ade Firman',
                'email'    => 'adefirman768@gmail.com',
                'role'     => 'juru-bengkel',
                'password' => Hash::make('123123123'),
            ],
            [
                'name'     => 'Muh Pratama Mukti ilmianto',
                'email'    => 'mpratama012@gmail.com',
                'role'     => 'juru-bengkel',
                'password' => Hash::make('123123123'),
            ],
            [
                'name'     => 'Heriyadi',
                'email'    => 'heriyadiy4@gmail.com',
                'role'     => 'juru-bengkel',
                'password' => Hash::make('123123123'),
            ],
            [
                'name'     => 'Mohammad Rahmat',
                'email'    => 'rahmatboy7124@gmail.com',
                'role'     => 'juru-bengkel',
                'password' => Hash::make('123123123'),
            ],
            [
                'name'     => 'M Agus Fadillah',
                'email'    => 'magusfadillah84@gmail.com',
                'role'     => 'juru-bengkel',
                'password' => Hash::make('123123123'),
            ],

            // Data Reviewer
            [
                'name'     => 'Angga',
                'email'    => 'anggasukmanika@gmail.com',
                'role'     => 'reviewer',
                'password' => Hash::make('123123123'),
            ],
            [
                'name'     => 'Nia Kurniawati',
                'email'    => 'niakurniawati2009@gmail.com',
                'role'     => 'reviewer',
                'password' => Hash::make('123123123'),
            ],
            [
                'name'     => 'Awalusilman',
                'email'    => 'awalende@gmail.com',
                'role'     => 'reviewer',
                'password' => Hash::make('123123123'),
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->insert($user);
        }

        $this->call([
            LocationSeeder::class,
            SettingSeeder::class,
        ]);
    }
}
