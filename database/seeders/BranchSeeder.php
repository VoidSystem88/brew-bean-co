<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        $branches = [
            [
                'name' => 'Downtown Coffee Hub',
                'location' => '123 Main Street, Downtown',
                'contact_number' => '555-0101',
                'is_active' => true,
            ],
            [
                'name' => 'Riverside Brew',
                'location' => '456 River Road, Riverside',
                'contact_number' => '555-0102',
                'is_active' => true,
            ],
            [
                'name' => 'Central Perk',
                'location' => '789 Central Avenue, Midtown',
                'contact_number' => '555-0103',
                'is_active' => true,
            ],
        ];

        foreach ($branches as $branch) {
            Branch::create($branch);
        }
    }
}