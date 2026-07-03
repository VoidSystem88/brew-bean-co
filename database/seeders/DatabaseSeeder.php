<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            BranchSeeder::class,
            AdminUserSeeder::class,
            ProductSeeder::class,
            ItemSeeder::class,
            SupplierSeeder::class, // Add this
        ]);
    }
}
