<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Organization;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Str;

class OrganizationSeeder extends Seeder
{
    public function run(): void
    {
        // Create demo organization
        $organization = Organization::create([
            'name' => 'Demo Company',
            'slug' => 'demo-company',
            'email' => 'info@democompany.com',
            'phone' => '1234567890',
            'address' => '123 Business Street',
            'city' => 'Mumbai',
            'state' => 'Maharashtra',
            'pincode' => '400001',
            'country' => 'India',
            'is_active' => true,
        ]);

        // Create admin user if not exists
        $adminRole = Role::where('name', 'admin')->first();
        
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'role_id' => $adminRole->id,
            ]
        );

        // Attach admin user to organization
        $organization->addUser($adminUser, $adminRole->id, true);
        
        // Set current organization for admin
        $adminUser->update(['current_organization_id' => $organization->id]);

        // Create manager user
        $managerRole = Role::where('name', 'manager')->first();
        
        $managerUser = User::firstOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name' => 'Manager User',
                'password' => bcrypt('password'),
                'role_id' => $managerRole->id,
            ]
        );

        $organization->addUser($managerUser, $managerRole->id, true);
        $managerUser->update(['current_organization_id' => $organization->id]);

        // Create agent user
        $agentRole = Role::where('name', 'agent')->first();
        
        $agentUser = User::firstOrCreate(
            ['email' => 'agent@example.com'],
            [
                'name' => 'Sales Agent',
                'password' => bcrypt('password'),
                'role_id' => $agentRole->id,
            ]
        );

        $organization->addUser($agentUser, $agentRole->id, true);
        $agentUser->update(['current_organization_id' => $organization->id]);

        $this->command->info('Demo organization and users created successfully!');
        $this->command->info('Admin: admin@example.com / password');
        $this->command->info('Manager: manager@example.com / password');
        $this->command->info('Agent: agent@example.com / password');
    }
}