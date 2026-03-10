<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LeadStatus;

class LeadStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            [
                'name' => 'new',
                'display_name' => 'New',
                'color' => '#3B82F6',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'contacted',
                'display_name' => 'Contacted',
                'color' => '#8B5CF6',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'qualified',
                'display_name' => 'Qualified',
                'color' => '#F59E0B',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'proposal_sent',
                'display_name' => 'Proposal Sent',
                'color' => '#06B6D4',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'negotiation',
                'display_name' => 'Negotiation',
                'color' => '#F97316',
                'order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'converted',
                'display_name' => 'Converted',
                'color' => '#10B981',
                'order' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'lost',
                'display_name' => 'Lost',
                'color' => '#EF4444',
                'order' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'not_interested',
                'display_name' => 'Not Interested',
                'color' => '#6B7280',
                'order' => 8,
                'is_active' => true,
            ],
        ];

        foreach ($statuses as $status) {
            LeadStatus::updateOrCreate(
                ['name' => $status['name']],
                $status
            );
        }
    }
}