<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Managing Partner
        User::create([
            'name' => 'John Smith',
            'email' => 'john@lawfirm.com',
            'role' => 'managing_partner',
            'hourly_rate' => 450.00,
            'phone' => '555-0101',
            'bar_number' => 'TX123456',
            'bio' => 'Managing Partner with 20+ years experience in consumer protection law.',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // Attorneys
        User::create([
            'name' => 'Sarah Johnson',
            'email' => 'sarah@lawfirm.com',
            'role' => 'attorney',
            'hourly_rate' => 350.00,
            'phone' => '555-0102',
            'bar_number' => 'TX234567',
            'bio' => 'Senior Attorney specializing in FCRA litigation.',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Michael Davis',
            'email' => 'michael@lawfirm.com',
            'role' => 'attorney',
            'hourly_rate' => 320.00,
            'phone' => '555-0103',
            'bar_number' => 'TX345678',
            'bio' => 'Attorney focused on consumer rights and credit reporting disputes.',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // Paralegals
        User::create([
            'name' => 'Lisa Chen',
            'email' => 'lisa@lawfirm.com',
            'role' => 'paralegal',
            'hourly_rate' => 125.00,
            'phone' => '555-0104',
            'bio' => 'Senior Paralegal with expertise in case management and document preparation.',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Robert Wilson',
            'email' => 'robert@lawfirm.com',
            'role' => 'paralegal',
            'hourly_rate' => 110.00,
            'phone' => '555-0105',
            'bio' => 'Paralegal specializing in discovery and client communication.',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // Intake Team
        User::create([
            'name' => 'Amanda Rodriguez',
            'email' => 'amanda@lawfirm.com',
            'role' => 'intake_team',
            'hourly_rate' => 85.00,
            'phone' => '555-0106',
            'bio' => 'Client Intake Specialist focused on initial client consultations.',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // Admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@lawfirm.com',
            'role' => 'admin',
            'hourly_rate' => 0.00,
            'phone' => '555-0107',
            'bio' => 'System Administrator',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
    }
}