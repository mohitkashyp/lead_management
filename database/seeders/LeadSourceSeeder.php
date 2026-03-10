<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LeadSource;

class LeadSourceSeeder extends Seeder
{
    public function run(): void
    {
        $sources = [
            [
                'name' => 'facebook',
                'display_name' => 'Facebook',
                'icon' => 'facebook',
                'color' => '#1877F2',
                'is_active' => true,
            ],
            [
                'name' => 'instagram',
                'display_name' => 'Instagram',
                'icon' => 'instagram',
                'color' => '#E4405F',
                'is_active' => true,
            ],
            [
                'name' => 'indiamart',
                'display_name' => 'IndiaMART',
                'icon' => 'shopping-bag',
                'color' => '#FF6B00',
                'is_active' => true,
            ],
            [
                'name' => 'word_of_mouth',
                'display_name' => 'Word of Mouth',
                'icon' => 'users',
                'color' => '#10B981',
                'is_active' => true,
            ],
            [
                'name' => 'website',
                'display_name' => 'Website',
                'icon' => 'globe',
                'color' => '#3B82F6',
                'is_active' => true,
            ],
            [
                'name' => 'google_ads',
                'display_name' => 'Google Ads',
                'icon' => 'search',
                'color' => '#4285F4',
                'is_active' => true,
            ],
            [
                'name' => 'linkedin',
                'display_name' => 'LinkedIn',
                'icon' => 'linkedin',
                'color' => '#0A66C2',
                'is_active' => true,
            ],
            [
                'name' => 'whatsapp',
                'display_name' => 'WhatsApp',
                'icon' => 'message-circle',
                'color' => '#25D366',
                'is_active' => true,
            ],
            [
                'name' => 'email',
                'display_name' => 'Email Campaign',
                'icon' => 'mail',
                'color' => '#6366F1',
                'is_active' => true,
            ],
            [
                'name' => 'other',
                'display_name' => 'Other',
                'icon' => 'more-horizontal',
                'color' => '#6B7280',
                'is_active' => true,
            ],
        ];

        foreach ($sources as $source) {
            LeadSource::updateOrCreate(
                ['name' => $source['name']],
                $source
            );
        }
    }
}