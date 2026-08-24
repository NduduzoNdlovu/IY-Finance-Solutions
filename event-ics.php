<?php
declare(strict_types=1);

require __DIR__ . '/config/bootstrap.php';
require PROJECT_ROOT . '/config/content.php';

$id = trim((string) ($_GET['id'] ?? ''));
$event = content_find_by_id(content_public_events(), $id);
if ($event === null) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Event not found.';
    exit;
}

$zone = new DateTimeZone('Africa/Johannesburg');
$startDate = (string) $event['start_date'];
$storedEndDate = (string) ($event['end_date'] ?? '');
$endDate = $storedEndDate !== '' ? $storedEndDate : $startDate;
$startTime = (string) ($event['start_time'] ?? '');
$endTime = (string) ($event['end_time'] ?? '');
$lines = ['BEGIN:VCALENDAR', 'VERSION:2.0', 'PRODID:-//IY Finance Solutions//Events//EN', 'CALSCALE:GREGORIAN', 'METHOD:PUBLISH', 'BEGIN:VEVENT'];
$lines[] = 'UID:' . content_ics_escape((string) $event['id']) . '@iyfinancesolutions.co.za';
$lines[] = 'DTSTAMP:' . gmdate('Ymd\THis\Z');

if ($startTime === '') {
    $start = new DateTimeImmutable($startDate, $zone);
    $end = (new DateTimeImmutable($endDate, $zone))->modify('+1 day');
    $lines[] = 'DTSTART;VALUE=DATE:' . $start->format('Ymd');
    $lines[] = 'DTEND;VALUE=DATE:' . $end->format('Ymd');
} else {
    $start = new DateTimeImmutable($startDate . ' ' . $startTime, $zone);
    if ($endTime !== '') {
        $end = new DateTimeImmutable($endDate . ' ' . $endTime, $zone);
    } elseif ($endDate !== $startDate) {
        $end = new DateTimeImmutable($endDate . ' 23:59', $zone);
    } else {
        $end = $start->modify('+1 hour');
    }
    $lines[] = 'DTSTART;TZID=Africa/Johannesburg:' . $start->format('Ymd\THis');
    $lines[] = 'DTEND;TZID=Africa/Johannesburg:' . $end->format('Ymd\THis');
}

$lines[] = 'SUMMARY:' . content_ics_escape((string) $event['title']);
$lines[] = 'DESCRIPTION:' . content_ics_escape((string) ($event['description'] ?? ''));
$lines[] = 'LOCATION:' . content_ics_escape(trim((string) ($event['location'] ?? '') . ', ' . (string) ($event['address'] ?? ''), ' ,'));
$lines[] = 'URL:' . content_ics_escape(content_absolute_url('events.php#event-' . (string) $event['id']));
$lines[] = 'END:VEVENT';
$lines[] = 'END:VCALENDAR';

$filename = content_slug((string) $event['title']) . '.ics';
header('Content-Type: text/calendar; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: private, max-age=300');
echo implode("\r\n", $lines) . "\r\n";
