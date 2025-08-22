<?php

namespace App\Services;

use App\Models\CalendarEvent;
use App\Models\User;
use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use Illuminate\Support\Facades\Log;

class GoogleCalendarService
{
    private Client $client;
    private Calendar $service;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setApplicationName(config('app.name'));
        $this->client->setScopes([Calendar::CALENDAR]);
        $this->client->setAuthConfig(storage_path('app/google-calendar-credentials.json'));
        $this->client->setAccessType('offline');
        
        $this->service = new Calendar($this->client);
    }

    /**
     * Sync calendar event to Google Calendar
     */
    public function syncEventToGoogle(CalendarEvent $event): bool
    {
        try {
            // Get user's Google Calendar ID (primary for now)
            $calendarId = 'primary';

            $googleEvent = new Event([
                'summary' => $event->title,
                'description' => $event->description,
                'start' => [
                    'dateTime' => $event->start_datetime->toISOString(),
                    'timeZone' => config('app.timezone'),
                ],
                'end' => [
                    'dateTime' => $event->end_datetime->toISOString(),
                    'timeZone' => config('app.timezone'),
                ],
                'location' => $event->location,
                'attendees' => $this->formatAttendees($event->attendees),
                'reminders' => $this->formatReminders($event->reminders),
            ]);

            if ($event->google_event_id) {
                // Update existing event
                $result = $this->service->events->update($calendarId, $event->google_event_id, $googleEvent);
            } else {
                // Create new event
                $result = $this->service->events->insert($calendarId, $googleEvent);
                $event->update(['google_event_id' => $result->getId()]);
            }

            $event->update([
                'synced_with_google' => true,
                'last_google_sync' => now(),
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Google Calendar sync failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete event from Google Calendar
     */
    public function deleteEventFromGoogle(CalendarEvent $event): bool
    {
        try {
            if (!$event->google_event_id) {
                return true; // Nothing to delete
            }

            $calendarId = 'primary';
            $this->service->events->delete($calendarId, $event->google_event_id);

            $event->update([
                'google_event_id' => null,
                'synced_with_google' => false,
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Google Calendar delete failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Import events from Google Calendar
     */
    public function importEventsFromGoogle(User $user, \DateTime $startDate = null, \DateTime $endDate = null): array
    {
        try {
            $calendarId = 'primary';
            $optParams = [
                'maxResults' => 100,
                'orderBy' => 'startTime',
                'singleEvents' => true,
            ];

            if ($startDate) {
                $optParams['timeMin'] = $startDate->format(\DateTime::ATOM);
            }

            if ($endDate) {
                $optParams['timeMax'] = $endDate->format(\DateTime::ATOM);
            }

            $events = $this->service->events->listEvents($calendarId, $optParams);
            $importedEvents = [];

            foreach ($events->getItems() as $googleEvent) {
                $existingEvent = CalendarEvent::where('google_event_id', $googleEvent->getId())->first();

                if (!$existingEvent) {
                    $event = CalendarEvent::create([
                        'title' => $googleEvent->getSummary(),
                        'description' => $googleEvent->getDescription(),
                        'start_datetime' => $googleEvent->getStart()->getDateTime(),
                        'end_datetime' => $googleEvent->getEnd()->getDateTime(),
                        'location' => $googleEvent->getLocation(),
                        'google_event_id' => $googleEvent->getId(),
                        'synced_with_google' => true,
                        'last_google_sync' => now(),
                        'user_id' => $user->id,
                        'type' => 'other',
                        'status' => 'scheduled',
                    ]);

                    $importedEvents[] = $event;
                }
            }

            return $importedEvents;

        } catch (\Exception $e) {
            Log::error('Google Calendar import failed: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Check for conflicts with existing events
     */
    public function checkForConflicts(CalendarEvent $newEvent, User $user): array
    {
        $conflicts = CalendarEvent::where('user_id', $user->id)
            ->where('id', '!=', $newEvent->id)
            ->where('status', 'scheduled')
            ->where(function ($query) use ($newEvent) {
                $query->whereBetween('start_datetime', [$newEvent->start_datetime, $newEvent->end_datetime])
                    ->orWhereBetween('end_datetime', [$newEvent->start_datetime, $newEvent->end_datetime])
                    ->orWhere(function ($q) use ($newEvent) {
                        $q->where('start_datetime', '<=', $newEvent->start_datetime)
                          ->where('end_datetime', '>=', $newEvent->end_datetime);
                    });
            })
            ->get();

        return $conflicts->toArray();
    }

    /**
     * Format attendees for Google Calendar
     */
    private function formatAttendees(?array $attendees): array
    {
        if (!$attendees) {
            return [];
        }

        return array_map(function ($email) {
            return ['email' => $email];
        }, $attendees);
    }

    /**
     * Format reminders for Google Calendar
     */
    private function formatReminders(?array $reminders): array
    {
        if (!$reminders) {
            return ['useDefault' => true];
        }

        $overrides = [];
        foreach ($reminders as $type => $minutes) {
            $overrides[] = [
                'method' => $type === 'email' ? 'email' : 'popup',
                'minutes' => (int) $minutes,
            ];
        }

        return [
            'useDefault' => false,
            'overrides' => $overrides,
        ];
    }

    /**
     * Set up OAuth authentication URL
     */
    public function getAuthUrl(): string
    {
        return $this->client->createAuthUrl();
    }

    /**
     * Handle OAuth callback and save tokens
     */
    public function handleCallback(string $code, User $user): bool
    {
        try {
            $token = $this->client->fetchAccessTokenWithAuthCode($code);
            
            if (isset($token['error'])) {
                Log::error('Google OAuth error: ' . $token['error']);
                return false;
            }

            // Save token to user's metadata or separate table
            $user->update([
                'permissions' => array_merge($user->permissions ?? [], [
                    'google_calendar_token' => $token
                ])
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Google OAuth callback failed: ' . $e->getMessage());
            return false;
        }
    }
}