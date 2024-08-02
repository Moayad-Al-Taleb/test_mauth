<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'Roles View Roles',
            'Roles Add Role',
            'Roles View Role By ID',
            'Roles Edit Role',
            'Roles Delete Role',

            'View Posts',
            'Add Post',
            'View Post By ID',
            'Edit Post',
            'Archive Post',
            'View Archived Posts',
            'Restore Post',
            'Delete Post',
        ];

        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission
            ]);
        }
    }
}
