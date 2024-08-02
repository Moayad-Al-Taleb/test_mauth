<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * This method seeds the database with initial permissions.
     */
    public function run(): void
    {
        $permissions = [
            // Permissions related to role management
            'Roles View Roles',
            'Roles Add Role',
            'Roles View Role By ID',
            'Roles Edit Role',
            'Roles Delete Role',

            // Permissions related to post management
            'View Posts',
            'Add Post',
            'View Post By ID',
            'Edit Post',
            'Archive Post',
            'View Archived Posts',
            'Restore Post',
            'Delete Post',
        ];

        // Iterate over each permission and create it in the database
        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission  // Set the name of the permission
            ]);
        }
    }
}
