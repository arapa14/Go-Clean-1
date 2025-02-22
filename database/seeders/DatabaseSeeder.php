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
                'name'     => 'Caraka',
                'email'    => 'caraka@gmail.com',
                'role'     => 'petugas-kebersihan',
                'password' => Hash::make('123123123'),
            ],
            [
                'name'     => 'Reviewer',
                'email'    => 'reviewer@gmail.com',
                'role'     => 'reviewer',
                'password' => Hash::make('123123123'),
            ],
            [
                'name'     => 'Admin',
                'email'    => 'admin@gmail.com',
                'role'     => 'admin',
                'password' => Hash::make('lorem-ipsum'),
            ],

            // Data Caraka
            [
                'name'     => 'Ari Rusdiana',
                'email'    => 'arirusdiana92@gmail.com',
                'role'     => 'petugas-kebersihan',
                'password' => '$2y$12$XTZurRPb.4UYaTe2mxZEtel5bKYDRLUxyMXnQ8HOZM5sc91KPXfFi',
            ],
            [
                'name'     => 'Suhendi',
                'email'    => 'suhend807@gmail.com',
                'role'     => 'petugas-kebersihan',
                'password' => '$2y$12$IwY0IbxG/4CSdAqfXcn71ec3kz.ZKfbUwQbsIGjkGy2whqX7xQf7m',
            ],
            [
                'name'     => 'Herry Kurniawan',
                'email'    => 'herry19933@gmail.com',
                'role'     => 'petugas-kebersihan',
                'password' => '$2y$12$ZVfA2IOjxlE.VCXwdBPtces8YYYTGvAsiXqog8zIVuWBDoOSGLHXW',
            ],
            [
                'name'     => 'Devian Sukma Setiawan',
                'email'    => 'devianalrescha63@gmail.com',
                'role'     => 'petugas-kebersihan',
                'password' => '$2y$12$pyB3ImU.ryaxAzJjFSF0rOifE.VIFv2aUwvV.z6zABYwf/fwM4mz6',
            ],
            [
                'name'     => 'Hermanudin',
                'email'    => 'herman251283@gmail.com',
                'role'     => 'petugas-kebersihan',
                'password' => '$2y$12$z/erN6ywUWMGRnQvHr2M6eAHZX8nvA99xzNRWT1cGXfE2dxFdjYaG',
            ],
            [
                'name'     => 'Fauji Mutaqin',
                'email'    => 'ubaylulu20@gmail.com',
                'role'     => 'petugas-kebersihan',
                'password' => '$2y$12$9qXZXP4ucWxZe1btTBRWSeQUM1ilyZQSLNLjS4O7mj9a.KmYfqUKO',
            ],
            [
                'name'     => 'Darojatun Agung',
                'email'    => 'dragtrisan@gmail.com',
                'role'     => 'petugas-kebersihan',
                'password' => '$2y$12$8R1shkZ/ss3RxSnvMVotP.uVoKn5KXlpWmpkhgvsJg585vPT67Kqa',
            ],
            [
                'name'     => 'Muhamad Ihsanto',
                'email'    => 'iksanxme@gmail.com',
                'role'     => 'petugas-kebersihan',
                'password' => '$2y$12$VbZdLjGDmt/2eUVAj.VmLeIBH8rOud3qCLvg/Iph8lfT6Yw9QaAQ.',
            ],
            [
                'name'     => 'ENDANG',
                'email'    => 'yeyetendang1@gmail.com',
                'role'     => 'petugas-kebersihan',
                'password' => '$2y$12$YKwOm7.It0URH.RXXcPfr.TBRRvY2zhA/svRENMSBMRiZ8Ho2w1oS',
            ],

            // Data Toolmen (disimpan dengan role 'caraka' namun aslinya toolmen)
            [
                'name'     => 'Ade Firman',
                'email'    => 'adefirman768@gmail.com',
                'role'     => 'juru-bengkel', // Aslinya toolmen
                'password' => '$2y$12$bjQIAxZmvRaOA/HILOfCuOvtdwgnrY7tJiczwEPuXUXUSdN4yAMPe',
            ],
            [
                'name'     => 'Muh Pratama Mukti ilmianto',
                'email'    => 'mpratama012@gmail.com',
                'role'     => 'juru-bengkel', // Aslinya toolmen
                'password' => '$2y$12$CjAtJxVZw2R6lLeCiKFw5urfZ2N8xwO8aWXVurrmtgk8EMIGkkSJq',
            ],
            [
                'name'     => 'Heriyadi',
                'email'    => 'heriyadiy4@gmail.com',
                'role'     => 'juru-bengkel', // Aslinya toolmen
                'password' => '$2y$12$pnZTkwOAGWfNo9MiM0xYHe39u3pRE7dQLYU/UeIgGmw17ES0ijkHa',
            ],
            [
                'name'     => 'Mohammad Rahmat',
                'email'    => 'rahmatboy7124@gmail.com',
                'role'     => 'juru-bengkel', // Aslinya toolmen
                'password' => '$2y$12$l4QjScGvWup2kmvIqy3qeewHG1S2YTju1XLNsug.7whHE3q1LG1j6',
            ],
            [
                'name'     => 'M Agus Fadillah',
                'email'    => 'magusfadillah84@gmail.com',
                'role'     => 'juru-bengkel', // Aslinya toolmen
                'password' => '$2y$12$19you/4ykE.b7zbbs1UQxu4RxYrL9pxNvtXO4ICTbHdKgfjhwRqXO',
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
