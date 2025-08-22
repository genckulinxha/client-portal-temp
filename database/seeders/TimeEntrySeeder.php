<?php

namespace Database\Seeders;

use App\Models\TimeEntry;
use App\Models\User;
use App\Models\CaseModel;
use App\Models\Task;
use Illuminate\Database\Seeder;

class TimeEntrySeeder extends Seeder
{
    public function run(): void
    {
        $users = User::whereIn('role', ['attorney', 'paralegal'])->get();
        $cases = CaseModel::all();
        $tasks = Task::where('type', 'internal_task')->get();
        $attorney = User::where('role', 'attorney')->first();
        $paralegal = User::where('role', 'paralegal')->first();

        // Attorney time entries
        TimeEntry::create([
            'user_id' => $attorney->id,
            'case_id' => $cases->where('case_number', 'CASE-2025-0001')->first()->id,
            'hours' => 2.5,
            'description' => 'Research FCRA case law and precedents for Thompson case',
            'date' => now()->subDays(7),
            'start_time' => '09:00',
            'end_time' => '11:30',
            'billable' => true,
            'hourly_rate' => 350.00,
            'status' => 'approved',
        ]);

        TimeEntry::create([
            'user_id' => $attorney->id,
            'case_id' => $cases->where('case_number', 'CASE-2025-0001')->first()->id,
            'task_id' => $tasks->where('title', 'Draft Initial Complaint')->first()?->id,
            'hours' => 3.0,
            'description' => 'Draft initial complaint for Thompson v. Experian',
            'date' => now()->subDays(5),
            'start_time' => '13:00',
            'end_time' => '16:00',
            'billable' => true,
            'hourly_rate' => 350.00,
            'status' => 'submitted',
        ]);

        TimeEntry::create([
            'user_id' => $attorney->id,
            'case_id' => $cases->where('case_number', 'CASE-2025-0002')->first()->id,
            'hours' => 1.5,
            'description' => 'Client consultation and case strategy discussion',
            'date' => now()->subDays(4),
            'start_time' => '14:00',
            'end_time' => '15:30',
            'billable' => true,
            'hourly_rate' => 350.00,
            'status' => 'approved',
        ]);

        TimeEntry::create([
            'user_id' => $attorney->id,
            'case_id' => $cases->where('case_number', 'CASE-2025-0003')->first()->id,
            'hours' => 2.0,
            'description' => 'Settlement negotiation with opposing counsel',
            'date' => now()->subDays(2),
            'start_time' => '10:00',
            'end_time' => '12:00',
            'billable' => true,
            'hourly_rate' => 350.00,
            'status' => 'draft',
        ]);

        // Paralegal time entries
        TimeEntry::create([
            'user_id' => $paralegal->id,
            'case_id' => $cases->where('case_number', 'CASE-2025-0001')->first()->id,
            'hours' => 4.0,
            'description' => 'Organize case file and prepare discovery documents',
            'date' => now()->subDays(6),
            'start_time' => '09:00',
            'end_time' => '13:00',
            'billable' => true,
            'hourly_rate' => 125.00,
            'status' => 'approved',
        ]);

        TimeEntry::create([
            'user_id' => $paralegal->id,
            'case_id' => $cases->where('case_number', 'CASE-2025-0002')->first()->id,
            'task_id' => $tasks->where('title', 'Organize Case Discovery')->first()?->id,
            'hours' => 3.5,
            'description' => 'Review and categorize client-provided documents',
            'date' => now()->subDays(3),
            'start_time' => '14:00',
            'end_time' => '17:30',
            'billable' => true,
            'hourly_rate' => 125.00,
            'status' => 'submitted',
        ]);

        TimeEntry::create([
            'user_id' => $paralegal->id,
            'case_id' => $cases->where('case_number', 'CASE-2025-0001')->first()->id,
            'hours' => 1.0,
            'description' => 'File motion documents with court clerk',
            'date' => now()->subDays(1),
            'start_time' => '11:00',
            'end_time' => '12:00',
            'billable' => true,
            'hourly_rate' => 125.00,
            'status' => 'approved',
        ]);

        // Non-billable administrative time
        TimeEntry::create([
            'user_id' => $attorney->id,
            'hours' => 1.0,
            'description' => 'Weekly team meeting and case review',
            'date' => now()->subDays(7),
            'start_time' => '09:00',
            'end_time' => '10:00',
            'billable' => false,
            'status' => 'approved',
        ]);

        TimeEntry::create([
            'user_id' => $paralegal->id,
            'hours' => 0.5,
            'description' => 'CLE training on new FCRA regulations',
            'date' => now()->subDays(5),
            'start_time' => '16:00',
            'end_time' => '16:30',
            'billable' => false,
            'status' => 'approved',
        ]);

        // Different attorney entries
        $attorney2 = User::where('role', 'attorney')->skip(1)->first();
        if ($attorney2) {
            TimeEntry::create([
                'user_id' => $attorney2->id,
                'case_id' => $cases->where('case_number', 'CASE-2025-0002')->first()->id,
                'hours' => 2.5,
                'description' => 'Legal research on debt collection practices',
                'date' => now()->subDays(4),
                'start_time' => '10:00',
                'end_time' => '12:30',
                'billable' => true,
                'hourly_rate' => 320.00,
                'status' => 'submitted',
            ]);

            TimeEntry::create([
                'user_id' => $attorney2->id,
                'case_id' => $cases->where('case_number', 'CASE-2024-0156')->first()->id,
                'hours' => 1.5,
                'description' => 'Final settlement document review and execution',
                'date' => now()->subDays(30),
                'billable' => true,
                'hourly_rate' => 320.00,
                'status' => 'billed',
            ]);
        }
    }
}