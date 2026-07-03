<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'Premium Coffee Supply Co.',
                'email' => 'orders@premiumcoffee.com',
                'phone' => '555-0100',
                'address' => '123 Coffee Lane, Seattle, WA 98101',
                'contact_person' => 'John Smith',
                'is_active' => true,
                'website' => 'https://premiumcoffee.com',
                'notes' => 'Main supplier for coffee beans and syrups'
            ],
            [
                'name' => 'Fresh Dairy Distributors',
                'email' => 'orders@freshdairy.com',
                'phone' => '555-0101',
                'address' => '456 Milk Road, Portland, OR 97201',
                'contact_person' => 'Jane Doe',
                'is_active' => true,
                'website' => 'https://freshdairy.com',
                'notes' => 'Supplies milk, cream, and dairy products'
            ],
            [
                'name' => 'Bakery Goods Supply',
                'email' => 'orders@bakerygoods.com',
                'phone' => '555-0102',
                'address' => '789 Pastry Ave, San Francisco, CA 94101',
                'contact_person' => 'Bob Johnson',
                'is_active' => true,
                'website' => 'https://bakerygoods.com',
                'notes' => 'Pastries, bread, and baked goods'
            ],
        ];

        foreach ($suppliers as $supplierData) {
            // Check if supplier already exists
            if (!Supplier::where('email', $supplierData['email'])->exists()) {
                Supplier::create($supplierData);
            }
        }
    }
}