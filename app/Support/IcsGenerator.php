<?php

namespace App\Support;

use App\Models\Event;
use Illuminate\Support\Carbon;

class IcsGenerator
{
    public static function forEvent(Event $event, ?string $attendeeEmail = null, ?string $attendeeName = null): string
    {
        $uid = 'event-'.$event->id.'@gemdtek.com';
        $start = $event->event_date instanceof Carbon ? $event->event_date->copy()->utc() : null;
        $end = $start?->copy()->addHours(2);
        $now = now()->utc();

        $title = $event->getTranslation('title', app()->getLocale(), false) ?: $event->getTranslation('title', 'tr');
        $description = $event->getTranslation('summary', app()->getLocale(), false)
            ?: $event->getTranslation('summary', 'tr')
            ?: '';
        $url = route('events.show', $event);
        $location = $event->location ?? '';

        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//GEMDTEK//Event Registrations//TR',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
            'BEGIN:VEVENT',
            'UID:'.$uid,
            'DTSTAMP:'.$now->format('Ymd\THis\Z'),
        ];

        if ($start) {
            $lines[] = 'DTSTART:'.$start->format('Ymd\THis\Z');
            $lines[] = 'DTEND:'.$end->format('Ymd\THis\Z');
        }

        $lines[] = 'SUMMARY:'.self::escape($title);
        if ($description) {
            $lines[] = 'DESCRIPTION:'.self::escape($description);
        }
        if ($location) {
            $lines[] = 'LOCATION:'.self::escape($location);
        }
        $lines[] = 'URL:'.$url;

        if ($attendeeEmail) {
            $cn = $attendeeName ? 'CN='.self::escape($attendeeName).':' : '';
            $lines[] = 'ATTENDEE;'.$cn.'mailto:'.$attendeeEmail;
        }

        $lines[] = 'STATUS:CONFIRMED';
        $lines[] = 'END:VEVENT';
        $lines[] = 'END:VCALENDAR';

        return implode("\r\n", $lines)."\r\n";
    }

    private static function escape(string $text): string
    {
        return str_replace(
            [',', ';', "\n", "\r"],
            ['\\,', '\\;', '\\n', ''],
            $text
        );
    }
}
