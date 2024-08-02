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
     *
     * This method seeds the database with initial user roles and permissions.
     */
    public function run(): void
    {
        // Create a default user with specific attributes
        $user = User::create([
            "name" => "Moayad Al-Taleb",    // User's name
            "email" => "mo@fahem.com",      // User's email address
            'phone' => '01012345678',       // User's phone number
            "password" => Hash::make(123456789), // User's password (hashed)
        ]);

        // Create an 'Admin' role
        $AdminRole = Role::create([
            'name' => 'Admin',              // Role name
        ]);

        // Retrieve all permissions and assign them to the 'Admin' role
        $AllPermissions = Permission::pluck('id')->all();  // Get all permission IDs
        $AdminRole->syncPermissions($AllPermissions);      // Sync permissions with the 'Admin' role

        // Assign the 'Admin' role to the created user
        $user->assignRole($AdminRole->id);  // Assign the 'Admin' role to the user by its ID

        // Create a 'Provider' role
        $ProviderRole = Role::create([
            'name' => 'Provider',           // Role name
        ]);

        // Retrieve specific permissions and assign them to the 'Provider' role
        $ProviderPermissions = Permission::whereIn('id', [6, 7, 8, 9, 10])->pluck('id')->all();  // Get specific permission IDs
        $ProviderRole->syncPermissions($ProviderPermissions);  // Sync permissions with the 'Provider' role

        // Create an 'Applicant' role
        $ApplicantRole = Role::create([
            'name' => 'Applicant',          // Role name
        ]);

        // Retrieve specific permissions and assign them to the 'Applicant' role
        $ApplicantPermissions = Permission::whereIn('id', [6])->pluck('id')->all();  // Get specific permission IDs
        $ApplicantRole->syncPermissions($ApplicantPermissions);  // Sync permissions with the 'Applicant' role
    }
}
