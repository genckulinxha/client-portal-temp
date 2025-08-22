<?php

namespace Database\Seeders;

use App\Models\CalendarEvent;
use App\Models\User;
use App\Models\Client;
use App\Models\CaseModel;
use Illuminate\Database\Seeder;

class CalendarEventSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $clients = Client::all();
        $cases = CaseModel::all();
        $attorney = User::where('role', 'attorney')->first();
        $paralegal = User::where('role', 'paralegal')->first();

        // Court Hearing
        CalendarEvent::create([
            'title' => 'Motion Hearing - Thompson v. Experian',
            'description' => 'Hearing for motion for summary judgment',
            'start_datetime' => now()->addDays(7)->setTime(10, 0),
            'end_datetime' => now()->addDays(7)->setTime(11, 30),
            'type' => 'court_hearing',
            'status' => 'scheduled',
            'user_id' => $attorney->id,
            'case_id' => $cases->where('case_number', 'CASE-2025-0001')->first()->id,
            'client_id' => $clients->where('email', 'jennifer.thompson@email.com')->first()->id,
            'location' => 'Harris County Civil Court, Room 304',
            'attendees' => ['jennifer.thompson@email.com', 'paralegal@lawfirm.com'],
            'reminders' => [
                'email' => 1440, // 24 hours
                'popup' => 60, // 1 hour
            ],
        ]);

        // Client Consultation
        CalendarEvent::create([
            'title' => 'Initial Consultation - David Martinez',
            'description' => 'Initial consultation for FCRA dispute case',
            'start_datetime' => now()->addDays(2)->setTime(14, 0),
            'end_datetime' => now()->addDays(2)->setTime(15, 0),
            'type' => 'consultation',
            'status' => 'scheduled',
            'user_id' => $attorney->id,
            'case_id' => $cases->where('case_number', 'CASE-2025-0002')->first()->id,
            'client_id' => $clients->where('email', 'david.martinez@email.com')->first()->id,
            'location' => 'Law Office Conference Room A',
            'attendees' => ['david.martinez@email.com'],
            'reminders' => [
                'email' => 1440, // 24 hours
                'sms' => 120, // 2 hours
            ],
        ]);

        // Team Meeting
        CalendarEvent::create([
            'title' => 'Weekly Case Review Meeting',
            'description' => 'Review active cases and upcoming deadlines',
            'start_datetime' => now()->addDays(1)->setTime(9, 0),
            'end_datetime' => now()->addDays(1)->setTime(10, 0),
            'type' => 'meeting',
            'status' => 'scheduled',
            'user_id' => $attorney->id,
            'location' => 'Main Conference Room',
            'attendees' => [
                'sarah@lawfirm.com',
                'michael@lawfirm.com',
                'lisa@lawfirm.com',
                'robert@lawfirm.com'
            ],
            'reminders' => [
                'popup' => 15, // 15 minutes
            ],
        ]);

        // Deposition
        CalendarEvent::create([
            'title' => 'Deposition - ABC Collections Representative',
            'description' => 'Deposition of ABC Collections compliance officer',
            'start_datetime' => now()->addDays(14)->setTime(13, 0),
            'end_datetime' => now()->addDays(14)->setTime(17, 0),
            'type' => 'deposition',
            'status' => 'scheduled',
            'user_id' => $attorney->id,
            'case_id' => $cases->where('case_number', 'CASE-2025-0002')->first()->id,
            'location' => 'Court Reporter Services, 1234 Main St',
            'attendees' => [
                'david.martinez@email.com',
                'opposing.counsel@abccollections.com'
            ],
        ]);

        // Settlement Conference
        CalendarEvent::create([
            'title' => 'Settlement Conference - Anderson v. Equifax',
            'description' => 'Mediated settlement conference',
            'start_datetime' => now()->addDays(5)->setTime(10, 0),
            'end_datetime' => now()->addDays(5)->setTime(16, 0),
            'type' => 'meeting',
            'status' => 'scheduled',
            'user_id' => $attorney->id,
            'case_id' => $cases->where('case_number', 'CASE-2025-0003')->first()->id,
            'client_id' => $clients->where('email', 'james.anderson@email.com')->first()->id,
            'location' => 'Mediation Center Downtown',
            'attendees' => [
                'james.anderson@email.com',
                'mediator@mediationcenter.com',
                'equifax.counsel@lawfirm.com'
            ],
        ]);

        // Deadline Reminder
        CalendarEvent::create([
            'title' => 'Discovery Deadline - Martinez Case',
            'description' => 'All discovery must be completed by this date',
            'start_datetime' => now()->addDays(30)->setTime(23, 59),
            'end_datetime' => now()->addDays(30)->setTime(23, 59),
            'all_day' => true,
            'type' => 'deadline',
            'status' => 'scheduled',
            'user_id' => $paralegal->id,
            'case_id' => $cases->where('case_number', 'CASE-2025-0002')->first()->id,
            'reminders' => [
                'email' => 10080, // 1 week
                'email_2' => 1440, // 1 day
            ],
        ]);

        // Completed Event
        CalendarEvent::create([
            'title' => 'Client Intake Meeting - Maria Garcia',
            'description' => 'Initial intake meeting with prospective client',
            'start_datetime' => now()->subDays(3)->setTime(15, 0),
            'end_datetime' => now()->subDays(3)->setTime(16, 0),
            'type' => 'consultation',
            'status' => 'completed',
            'user_id' => User::where('role', 'intake_team')->first()->id,
            'client_id' => $clients->where('email', 'maria.garcia@email.com')->first()->id,
            'location' => 'Law Office Conference Room B',
            'attendees' => ['maria.garcia@email.com'],
        ]);
    }
}