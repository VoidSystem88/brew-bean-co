<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DeliveryUserSeeder extends Seeder
{
    public function run(): void
    {
        // Check if rider already exists
        $rider1 = User::where('email', 'rider1@brewbeanco.com')->first();
        if (!$rider1) {
            User::create([
                'name' => 'Delivery Rider 1',
                'email' => 'rider1@brewbeanco.com',
                'password' => Hash::make('password'),
                'role' => 'delivery',
                'branch_id' => 1,
                'is_active' => true,
            ]);
            $this->command->info('? Delivery Rider 1 created');
        } else {
            $this->command->info('?? Delivery Rider 1 already exists');
        }

        $rider2 = User::where('email', 'rider2@brewbeanco.com')->first();
        if (!$rider2) {
            User::create([
                'name' => 'Delivery Rider 2',
                'email' => 'rider2@brewbeanco.com',
                'password' => Hash::make('password'),
                'role' => 'delivery',
                'branch_id' => 1,
                'is_active' => true,
            ]);
            $this->command->info('? Delivery Rider 2 created');
        } else {
            $this->command->info('?? Delivery Rider 2 already exists');
        }

        $this->command->info('?? Delivery users seeded successfully!');
        $this->command->info('');
        $this->command->info('Login credentials:');
        $this->command->info('  Email: rider1@brewbeanco.com');
        $this->command->info('  Email: rider2@brewbeanco.com');
        $this->command->info('  Password: password');
    }
}
