<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\Client;
use App\Models\User;
use App\Models\CaseModel;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $clients = Client::all();
        $users = User::all();
        $cases = CaseModel::all();
        $attorney = User::where('role', 'attorney')->first();
        $paralegal = User::where('role', 'paralegal')->first();

        // Client Tasks
        Task::create([
            'title' => 'Upload Credit Reports',
            'description' => 'Please upload your most recent credit reports from all three bureaus.',
            'type' => 'client_task',
            'status' => 'pending',
            'priority' => 'high',
            'assigned_to_client_id' => $clients->where('email', 'jennifer.thompson@email.com')->first()->id,
            'case_id' => $cases->where('case_number', 'CASE-2025-0001')->first()->id,
            'created_by_user_id' => $attorney->id,
            'due_date' => now()->addDays(3),
            'requirements' => [
                'documents' => ['Experian Report', 'Equifax Report', 'TransUnion Report'],
                'format' => 'PDF',
                'max_size' => '10MB'
            ],
        ]);

        Task::create([
            'title' => 'Sign Retainer Agreement',
            'description' => 'Please review and sign the retainer agreement for your case.',
            'type' => 'client_task',
            'status' => 'in_progress',
            'priority' => 'urgent',
            'assigned_to_client_id' => $clients->where('email', 'david.martinez@email.com')->first()->id,
            'case_id' => $cases->where('case_number', 'CASE-2025-0002')->first()->id,
            'created_by_user_id' => $attorney->id,
            'due_date' => now()->addDay(),
            'requirements' => [
                'documents' => ['Signed Retainer Agreement'],
                'signatures_required' => 1
            ],
        ]);

        Task::create([
            'title' => 'Provide Identity Theft Documentation',
            'description' => 'Upload police report and identity theft affidavit.',
            'type' => 'client_task',
            'status' => 'completed',
            'priority' => 'high',
            'assigned_to_client_id' => $clients->where('email', 'jennifer.thompson@email.com')->first()->id,
            'case_id' => $cases->where('case_number', 'CASE-2025-0001')->first()->id,
            'created_by_user_id' => $attorney->id,
            'completed_at' => now()->subDays(2),
            'completion_notes' => 'All required documentation received and reviewed.',
        ]);

        // Internal Tasks
        Task::create([
            'title' => 'Draft Initial Complaint',
            'description' => 'Prepare initial complaint for Thompson v. Experian case.',
            'type' => 'internal_task',
            'status' => 'in_progress',
            'priority' => 'high',
            'assigned_to_user_id' => $attorney->id,
            'case_id' => $cases->where('case_number', 'CASE-2025-0001')->first()->id,
            'created_by_user_id' => $attorney->id,
            'due_date' => now()->addDays(7),
        ]);

        Task::create([
            'title' => 'Organize Case Discovery',
            'description' => 'Organize and categorize all discovery documents for Martinez case.',
            'type' => 'internal_task',
            'status' => 'pending',
            'priority' => 'medium',
            'assigned_to_user_id' => $paralegal->id,
            'case_id' => $cases->where('case_number', 'CASE-2025-0002')->first()->id,
            'created_by_user_id' => $attorney->id,
            'due_date' => now()->addDays(5),
        ]);

        Task::create([
            'title' => 'Settlement Document Preparation',
            'description' => 'Prepare settlement agreement documents for Anderson case.',
            'type' => 'internal_task',
            'status' => 'pending',
            'priority' => 'urgent',
            'assigned_to_user_id' => $paralegal->id,
            'case_id' => $cases->where('case_number', 'CASE-2025-0003')->first()->id,
            'created_by_user_id' => $attorney->id,
            'due_date' => now()->addDays(2),
        ]);

        Task::create([
            'title' => 'File Court Documents',
            'description' => 'File motion for summary judgment in Thompson case.',
            'type' => 'internal_task',
            'status' => 'completed',
            'priority' => 'high',
            'assigned_to_user_id' => $paralegal->id,
            'case_id' => $cases->where('case_number', 'CASE-2025-0001')->first()->id,
            'created_by_user_id' => $attorney->id,
            'completed_at' => now()->subDays(1),
            'completion_notes' => 'Motion filed successfully. Court date scheduled.',
        ]);

        // General tasks not tied to specific cases
        Task::create([
            'title' => 'Review New Client Intake Form',
            'description' => 'Review and process new client intake form for potential FCRA case.',
            'type' => 'internal_task',
            'status' => 'pending',
            'priority' => 'medium',
            'assigned_to_user_id' => User::where('role', 'intake_team')->first()->id,
            'created_by_user_id' => $attorney->id,
            'due_date' => now()->addDays(3),
        ]);
    }
}