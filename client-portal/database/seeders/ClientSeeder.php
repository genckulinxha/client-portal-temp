<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        Client::create([
            'first_name' => 'Jennifer',
            'last_name' => 'Thompson',
            'email' => 'jennifer.thompson@email.com',
            'phone' => '555-1001',
            'address' => '123 Main Street',
            'city' => 'Houston',
            'state' => 'TX',
            'zip_code' => '77001',
            'date_of_birth' => '1985-06-15',
            'status' => 'active',
            'portal_access' => true,
            'portal_password' => 'client123',
            'intake_data' => [
                'credit_bureau_disputes' => ['Experian', 'Equifax'],
                'dispute_type' => 'Identity theft',
                'damages_claimed' => '$5000',
            ],
            'notes' => 'Client contacted us regarding identity theft issues on credit report.',
        ]);

        Client::create([
            'first_name' => 'David',
            'last_name' => 'Martinez',
            'email' => 'david.martinez@email.com',
            'phone' => '555-1002',
            'address' => '456 Oak Avenue',
            'city' => 'Dallas',
            'state' => 'TX',
            'zip_code' => '75201',
            'date_of_birth' => '1978-12-03',
            'status' => 'active',
            'portal_access' => true,
            'portal_password' => 'client123',
            'intake_data' => [
                'credit_bureau_disputes' => ['TransUnion'],
                'dispute_type' => 'Inaccurate reporting',
                'damages_claimed' => '$3500',
            ],
            'notes' => 'Inaccurate payment history being reported by debt collector.',
        ]);

        Client::create([
            'first_name' => 'Maria',
            'last_name' => 'Garcia',
            'email' => 'maria.garcia@email.com',
            'phone' => '555-1003',
            'address' => '789 Pine Street',
            'city' => 'Austin',
            'state' => 'TX',
            'zip_code' => '73301',
            'date_of_birth' => '1990-03-22',
            'status' => 'prospect',
            'portal_access' => false,
            'intake_data' => [
                'credit_bureau_disputes' => ['Experian', 'Equifax', 'TransUnion'],
                'dispute_type' => 'Mixed file',
                'damages_claimed' => '$8000',
            ],
            'notes' => 'Initial consultation scheduled. Mixed file issues across all three bureaus.',
        ]);

        Client::create([
            'first_name' => 'James',
            'last_name' => 'Anderson',
            'email' => 'james.anderson@email.com',
            'phone' => '555-1004',
            'address' => '321 Elm Street',
            'city' => 'San Antonio',
            'state' => 'TX',
            'zip_code' => '78201',
            'date_of_birth' => '1982-09-08',
            'status' => 'active',
            'portal_access' => true,
            'portal_password' => 'client123',
            'intake_data' => [
                'credit_bureau_disputes' => ['Equifax'],
                'dispute_type' => 'FCRA violation',
                'damages_claimed' => '$2500',
            ],
            'notes' => 'FCRA violation case - bureau failed to investigate dispute properly.',
        ]);

        Client::create([
            'first_name' => 'Ashley',
            'last_name' => 'Brown',
            'email' => 'ashley.brown@email.com',
            'phone' => '555-1005',
            'address' => '654 Maple Drive',
            'city' => 'Fort Worth',
            'state' => 'TX',
            'zip_code' => '76101',
            'date_of_birth' => '1988-11-17',
            'status' => 'closed',
            'portal_access' => false,
            'intake_data' => [
                'credit_bureau_disputes' => ['TransUnion'],
                'dispute_type' => 'Debt collector violation',
                'damages_claimed' => '$4000',
            ],
            'notes' => 'Case successfully resolved. Settlement reached.',
        ]);
    }
}