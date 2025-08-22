<?php

namespace Database\Seeders;

use App\Models\CaseModel;
use App\Models\Client;
use App\Models\User;
use App\Models\Defendant;
use Illuminate\Database\Seeder;

class CaseSeeder extends Seeder
{
    public function run(): void
    {
        $clients = Client::all();
        $attorneys = User::where('role', 'attorney')->get();
        $paralegals = User::where('role', 'paralegal')->get();

        // Case 1: Jennifer Thompson - Identity Theft
        $case1 = CaseModel::create([
            'client_id' => $clients->where('email', 'jennifer.thompson@email.com')->first()->id,
            'attorney_id' => $attorneys->first()->id,
            'paralegal_id' => $paralegals->first()->id,
            'case_number' => 'CASE-2025-0001',
            'case_title' => 'Thompson v. Experian Information Solutions',
            'case_type' => 'fcra_lawsuit',
            'status' => 'litigation',
            'description' => 'FCRA lawsuit regarding identity theft and failure to properly investigate disputes.',
            'potential_damages' => 5000.00,
            'statute_limitations' => '2027-06-15',
            'filed_date' => '2025-01-15',
            'notes' => 'Strong case with documented FCRA violations.',
        ]);

        // Add defendants for Case 1
        Defendant::create([
            'case_id' => $case1->id,
            'name' => 'Experian Information Solutions',
            'type' => 'credit_bureau',
            'address' => '475 Anton Blvd',
            'city' => 'Costa Mesa',
            'state' => 'CA',
            'zip_code' => '92626',
            'phone' => '714-830-7000',
            'violation_details' => 'Failed to properly investigate identity theft disputes.',
        ]);

        // Case 2: David Martinez - Inaccurate Reporting
        $case2 = CaseModel::create([
            'client_id' => $clients->where('email', 'david.martinez@email.com')->first()->id,
            'attorney_id' => $attorneys->skip(1)->first()->id,
            'paralegal_id' => $paralegals->skip(1)->first()->id,
            'case_number' => 'CASE-2025-0002',
            'case_title' => 'Martinez v. ABC Collections',
            'case_type' => 'fcra_dispute',
            'status' => 'investigation',
            'description' => 'Debt collector reporting inaccurate payment history.',
            'potential_damages' => 3500.00,
            'notes' => 'Gathering documentation for FCRA violations.',
        ]);

        Defendant::create([
            'case_id' => $case2->id,
            'name' => 'ABC Collections Inc.',
            'type' => 'debt_collector',
            'address' => '123 Collection Blvd',
            'city' => 'Phoenix',
            'state' => 'AZ',
            'zip_code' => '85001',
            'phone' => '602-555-0123',
            'violation_details' => 'Reporting inaccurate payment history despite disputes.',
        ]);

        // Case 3: James Anderson - FCRA Violation
        $case3 = CaseModel::create([
            'client_id' => $clients->where('email', 'james.anderson@email.com')->first()->id,
            'attorney_id' => $attorneys->first()->id,
            'paralegal_id' => $paralegals->first()->id,
            'case_number' => 'CASE-2025-0003',
            'case_title' => 'Anderson v. Equifax Inc.',
            'case_type' => 'fcra_lawsuit',
            'status' => 'settlement',
            'description' => 'FCRA violation - bureau failed to investigate dispute within 30 days.',
            'potential_damages' => 2500.00,
            'settlement_amount' => 1800.00,
            'notes' => 'Settlement negotiations in progress.',
        ]);

        Defendant::create([
            'case_id' => $case3->id,
            'name' => 'Equifax Information Services LLC',
            'type' => 'credit_bureau',
            'address' => '1550 Peachtree Street NW',
            'city' => 'Atlanta',
            'state' => 'GA',
            'zip_code' => '30309',
            'phone' => '404-885-8000',
            'violation_details' => 'Failed to complete investigation within required timeframe.',
        ]);

        // Case 4: Ashley Brown - Closed Case
        $case4 = CaseModel::create([
            'client_id' => $clients->where('email', 'ashley.brown@email.com')->first()->id,
            'attorney_id' => $attorneys->skip(1)->first()->id,
            'paralegal_id' => $paralegals->skip(1)->first()->id,
            'case_number' => 'CASE-2024-0156',
            'case_title' => 'Brown v. XYZ Debt Collectors',
            'case_type' => 'fcra_lawsuit',
            'status' => 'closed',
            'description' => 'Successful FCRA lawsuit against debt collector.',
            'potential_damages' => 4000.00,
            'settlement_amount' => 3200.00,
            'closed_date' => '2024-12-15',
            'notes' => 'Case successfully resolved with favorable settlement.',
        ]);

        Defendant::create([
            'case_id' => $case4->id,
            'name' => 'XYZ Debt Collectors',
            'type' => 'debt_collector',
            'address' => '789 Debt Lane',
            'city' => 'Chicago',
            'state' => 'IL',
            'zip_code' => '60601',
            'phone' => '312-555-0456',
            'violation_details' => 'FDCPA and FCRA violations in collection practices.',
        ]);
    }
}