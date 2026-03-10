<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Full system access and management',
            ],
            [
                'name' => 'manager',
                'display_name' => 'Manager',
                'description' => 'Can manage team and view all leads',
            ],
            [
                'name' => 'agent',
                'display_name' => 'Sales Agent',
                'description' => 'Can manage assigned leads and create orders',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['name' => $role['name']],
                $role
            );
        }
    }
}