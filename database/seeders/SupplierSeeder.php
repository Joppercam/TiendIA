<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'TechSupplies Inc.',
                'company_name' => 'TechSupplies Incorporated',
                'contact_person' => 'John Doe',
                'email' => 'contact@techsupplies.com',
                'phone' => '555-123-4567',
                'address' => '123 Tech Blvd',
                'city' => 'San Francisco',
                'state' => 'CA',
                'zip_code' => '94105',
                'country' => 'USA',
                'is_active' => true,
            ],
            [
                'name' => 'Global Electronics',
                'company_name' => 'Global Electronics LLC',
                'contact_person' => 'Jane Smith',
                'email' => 'jsmith@globalelectronics.com',
                'phone' => '555-987-6543',
                'address' => '456 Electronic Ave',
                'city' => 'Austin',
                'state' => 'TX',
                'zip_code' => '78701',
                'country' => 'USA',
                'is_active' => true,
            ],
            [
                'name' => 'Distribuidora Latinoamericana',
                'company_name' => 'Distribuidora Latinoamericana S.A.',
                'contact_person' => 'Carlos Ramírez',
                'email' => 'info@distribuidoralatam.com',
                'phone' => '+52 555 1234567',
                'address' => 'Av. Insurgentes 1000',
                'city' => 'Ciudad de México',
                'state' => 'CDMX',
                'zip_code' => '06100',
                'country' => 'México',
                'is_active' => true,
            ],
            [
                'name' => 'Asian Market Supplies',
                'company_name' => 'Asian Market Supplies Ltd.',
                'contact_person' => 'Li Wei',
                'email' => 'contact@asianmarketsupplies.com',
                'phone' => '+86 10 12345678',
                'address' => '789 East Road',
                'city' => 'Shenzhen',
                'state' => 'Guangdong',
                'zip_code' => '518000',
                'country' => 'China',
                'is_active' => true,
            ],
            [
                'name' => 'European Gadgets',
                'company_name' => 'European Gadgets GmbH',
                'contact_person' => 'Hans Mueller',
                'email' => 'info@europeangadgets.eu',
                'phone' => '+49 30 1234567',
                'address' => 'Hauptstrasse 100',
                'city' => 'Berlin',
                'state' => 'Berlin',
                'zip_code' => '10115',
                'country' => 'Germany',
                'is_active' => true,
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}