<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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
                'password' => '123456789',
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }

    }
}
