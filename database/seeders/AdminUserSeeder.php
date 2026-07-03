<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@beantrack.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'branch_id' => null,
            'is_active' => true,
        ]);

        // Branch Managers
        $managers = [
            ['name' => 'John Doe', 'email' => 'john@beantrack.com', 'branch_id' => 1],
            ['name' => 'Jane Smith', 'email' => 'jane@beantrack.com', 'branch_id' => 2],
        ];

        foreach ($managers as $manager) {
            User::create([
                'name' => $manager['name'],
                'email' => $manager['email'],
                'password' => Hash::make('password'),
                'role' => 'manager',
                'branch_id' => $manager['branch_id'],
                'is_active' => true,
            ]);
        }

        // Staff
        $staff = [
            ['name' => 'Alice Johnson', 'email' => 'alice@beantrack.com', 'branch_id' => 1],
            ['name' => 'Bob Wilson', 'email' => 'bob@beantrack.com', 'branch_id' => 1],
            ['name' => 'Carol Davis', 'email' => 'carol@beantrack.com', 'branch_id' => 2],
        ];

        foreach ($staff as $staffMember) {
            User::create([
                'name' => $staffMember['name'],
                'email' => $staffMember['email'],
                'password' => Hash::make('password'),
                'role' => 'staff',
                'branch_id' => $staffMember['branch_id'],
                'is_active' => true,
            ]);
        }
    }
}