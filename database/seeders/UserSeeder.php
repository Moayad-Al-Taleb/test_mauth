<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            "name" => "Moayad Al-Taleb",
            "email" => "mo@fahem.com",
            'phone' => '01012345678',
            "password" => Hash::make(123456789),
        ]);

        $AdminRole = Role::create(['name' => 'Admin']);
        $AllPermissions = Permission::pluck('id')->all();
        $AdminRole->syncPermissions($AllPermissions);
        $user->assignRole([$AdminRole->id]);

        $ProviderRole = Role::create(['name' => 'Provider']);
        $ProviderPermissions = Permission::whereIn('id', [6, 7, 8, 9, 10])->pluck('id')->all();
        $ProviderRole->syncPermissions($ProviderPermissions);

        $ApplicantRole = Role::create(['name' => 'Applicant']);
        $ApplicantPermissions = Permission::whereIn('id', [6])->pluck('id')->all();
        $ApplicantRole->syncPermissions($ApplicantPermissions);
    }
}
