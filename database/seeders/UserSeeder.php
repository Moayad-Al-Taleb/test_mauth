<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Moayad Al-Taleb',
                'email' => 'mo@fahem.com',
                'phone' => '01012345678',
                'password' => Hash::make(123456789),
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }

    }
}
