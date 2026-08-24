<?php
declare(strict_types=1);

if (!defined('PROJECT_ROOT') || !defined('STORAGE_DIRECTORY')) {
    throw new RuntimeException('Load config/bootstrap.php before config/content.php.');
}

defined('UPDATES_RECORD_FILE') || define('UPDATES_RECORD_FILE', STORAGE_DIRECTORY . '/updates.json');
defined('EVENTS_RECORD_FILE') || define('EVENTS_RECORD_FILE', STORAGE_DIRECTORY . '/events.json');
defined('UPDATES_UPLOAD_DIRECTORY') || define('UPDATES_UPLOAD_DIRECTORY', PROJECT_ROOT . '/uploads/updates');
defined('EVENTS_UPLOAD_DIRECTORY') || define('EVENTS_UPLOAD_DIRECTORY', PROJECT_ROOT . '/uploads/events');

function content_update_categories(): array
{
    return [
        'announcement' => 'Announcement',
        'flyer' => 'Promotional flyer',
        'branch' => 'Branch news',
        'offer' => 'Special offer',
        'hours' => 'Trading hours',
        'recognition' => 'Team recognition',
        'community' => 'Community update',
        'notice' => 'Customer notice',
    ];
}

function content_event_categories(): array
{
    return [
        'community' => 'Community event',
        'activation' => 'Branch activation',
        'workshop' => 'Financial workshop',
        'radio' => 'Radio or media appearance',
        'outreach' => 'Outreach programme',
        'internal' => 'Company event',
    ];
}

function content_read_records(string $file): array
{
    if (!is_file($file)) {
        return [];
    }

    $json = file_get_contents($file);
    if ($json === false || trim($json) === '') {
        return [];
    }

    $records = json_decode($json, true);
    if (!is_array($records)) {
        return [];
    }

    return array_values(array_filter($records, 'is_array'));
}

function content_write_records(string $file, array $records): bool
{
    $directory = dirname($file);
    if (!is_dir($directory) && !mkdir($directory, 0750, true) && !is_dir($directory)) {
        return false;
    }

    $encoded = json_encode(array_values($records), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if (!is_string($encoded)) {
        return false;
    }

    try {
        $suffix = bin2hex(random_bytes(6));
    } catch (Throwable $exception) {
        $suffix = str_replace('.', '', uniqid('', true));
    }

    $temporaryFile = $file . '.tmp-' . $suffix;
    if (file_put_contents($temporaryFile, $encoded . PHP_EOL, LOCK_EX) === false) {
        return false;
    }

    @chmod($temporaryFile, 0640);
    if (!rename($temporaryFile, $file)) {
        @unlink($temporaryFile);
        return false;
    }

    return true;
}

function content_find_by_id(array $records, string $id): ?array
{
    foreach ($records as $record) {
        if (($record['id'] ?? '') === $id) {
            return $record;
        }
    }
    return null;
}

function content_find_update_by_slug(array $records, string $slug): ?array
{
    foreach ($records as $record) {
        if (($record['slug'] ?? '') === $slug) {
            return $record;
        }
    }
    return null;
}

function content_new_id(string $prefix): string
{
    try {
        $random = bin2hex(random_bytes(5));
    } catch (Throwable $exception) {
        $random = substr(sha1(uniqid('', true)), 0, 10);
    }
    return $prefix . '-' . gmdate('YmdHis') . '-' . $random;
}

function content_slug(string $value): string
{
    $value = trim(function_exists('mb_strtolower') ? mb_strtolower($value, 'UTF-8') : strtolower($value));
    $transliterated = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
    if (is_string($transliterated)) {
        $value = $transliterated;
    }
    $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';
    return trim($value, '-') ?: 'update';
}

function content_unique_slug(string $title, array $records, ?string $ignoreId = null): string
{
    $base = content_slug($title);
    $slug = $base;
    $number = 2;

    while (true) {
        $used = false;
        foreach ($records as $record) {
            if (($record['id'] ?? null) !== $ignoreId && ($record['slug'] ?? '') === $slug) {
                $used = true;
                break;
            }
        }
        if (!$used) {
            return $slug;
        }
        $slug = $base . '-' . $number;
        $number++;
    }
}

function content_date_is_valid(string $date): bool
{
    $parsed = DateTimeImmutable::createFromFormat('!Y-m-d', $date);
    return $parsed instanceof DateTimeImmutable && $parsed->format('Y-m-d') === $date;
}

function content_time_is_valid(string $time): bool
{
    if ($time === '') {
        return true;
    }
    $parsed = DateTimeImmutable::createFromFormat('!H:i', $time);
    return $parsed instanceof DateTimeImmutable && $parsed->format('H:i') === $time;
}

function content_today(): string
{
    return (new DateTimeImmutable('now', new DateTimeZone('Africa/Johannesburg')))->format('Y-m-d');
}

function content_human_date(string $date): string
{
    if (!content_date_is_valid($date)) {
        return $date;
    }
    return (new DateTimeImmutable($date))->format('j F Y');
}

function content_human_time(string $time): string
{
    if (!content_time_is_valid($time) || $time === '') {
        return '';
    }
    return (new DateTimeImmutable($time))->format('H:i');
}

function content_excerpt(string $text, int $length = 150): string
{
    $text = trim(preg_replace('/\s+/u', ' ', $text) ?? $text);
    if (content_text_length($text) <= $length) {
        return $text;
    }
    $excerpt = function_exists('mb_substr') ? mb_substr($text, 0, $length - 1, 'UTF-8') : substr($text, 0, $length - 1);
    return rtrim($excerpt) . '…';
}

function content_text_length(string $value): int
{
    return function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : strlen($value);
}

function content_upload_image(array $file, string $directory, string $prefix): array
{
    $error = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
    if ($error === UPLOAD_ERR_NO_FILE) {
        throw new RuntimeException('No image was selected.');
    }
    if ($error !== UPLOAD_ERR_OK) {
        throw new RuntimeException('The image upload did not complete. Please try again.');
    }

    $temporaryName = (string) ($file['tmp_name'] ?? '');
    $size = (int) ($file['size'] ?? 0);
    if ($size <= 0 || $size > 8 * 1024 * 1024) {
        throw new RuntimeException('The image must be smaller than 8 MB.');
    }
    if (!is_uploaded_file($temporaryName)) {
        throw new RuntimeException('The selected file was not recognised as a valid upload.');
    }

    $allowedTypes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($temporaryName);
    $dimensions = @getimagesize($temporaryName);
    if (!is_string($mimeType) || !isset($allowedTypes[$mimeType]) || $dimensions === false) {
        throw new RuntimeException('Use a valid JPG, PNG or WEBP image.');
    }
    if ((int) $dimensions[0] > 12000 || (int) $dimensions[1] > 12000) {
        throw new RuntimeException('The image dimensions are too large.');
    }

    if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
        throw new RuntimeException('The upload folder could not be created. Check its cPanel permissions.');
    }
    if (!is_writable($directory)) {
        throw new RuntimeException('The upload folder is not writable. Check its cPanel permissions.');
    }

    $filename = $prefix . '-' . gmdate('Ymd-His') . '-' . bin2hex(random_bytes(7)) . '.' . $allowedTypes[$mimeType];
    $destination = $directory . '/' . $filename;
    if (!move_uploaded_file($temporaryName, $destination)) {
        throw new RuntimeException('The image could not be saved.');
    }
    @chmod($destination, 0644);

    return [
        'filename' => $filename,
        'width' => (int) $dimensions[0],
        'height' => (int) $dimensions[1],
    ];
}

function content_delete_image(string $filename, string $directory): bool
{
    if ($filename === '' || basename($filename) !== $filename) {
        return false;
    }
    $path = $directory . '/' . $filename;
    $realPath = realpath($path);
    $realDirectory = realpath($directory);
    if ($realPath === false || $realDirectory === false || dirname($realPath) !== $realDirectory || !is_file($realPath)) {
        return false;
    }
    return @unlink($realPath);
}

function content_media_url(string $type, string $filename): string
{
    $folder = $type === 'event' ? 'events' : 'updates';
    return site_url('uploads/' . $folder . '/' . rawurlencode(basename($filename)));
}

function content_url_is_valid(string $url): bool
{
    if ($url === '') {
        return true;
    }
    if (preg_match('#^(mailto:|tel:)#i', $url) === 1) {
        return true;
    }
    return filter_var($url, FILTER_VALIDATE_URL) !== false
        && in_array(strtolower((string) parse_url($url, PHP_URL_SCHEME)), ['http', 'https'], true);
}

function content_public_updates(bool $includeExpired = true): array
{
    $today = content_today();
    $records = array_values(array_filter(content_read_records(UPDATES_RECORD_FILE), static function (array $record) use ($today, $includeExpired): bool {
        $published = (string) ($record['published_on'] ?? '');
        $expires = (string) ($record['expires_on'] ?? '');
        if (empty($record['visible']) || !content_date_is_valid($published) || $published > $today) {
            return false;
        }
        return $includeExpired || $expires === '' || $expires >= $today;
    }));

    usort($records, static function (array $left, array $right): int {
        $featured = (int) !empty($right['featured']) <=> (int) !empty($left['featured']);
        return $featured !== 0 ? $featured : strcmp((string) ($right['published_on'] ?? ''), (string) ($left['published_on'] ?? ''));
    });
    return $records;
}

function content_update_is_expired(array $record): bool
{
    $expires = (string) ($record['expires_on'] ?? '');
    return $expires !== '' && content_date_is_valid($expires) && $expires < content_today();
}

function content_event_status(array $event): string
{
    $override = (string) ($event['status_override'] ?? '');
    if (in_array($override, ['cancelled', 'postponed'], true)) {
        return $override;
    }

    $today = content_today();
    $start = (string) ($event['start_date'] ?? '');
    $end = (string) ($event['end_date'] ?? '');
    $lastDay = content_date_is_valid($end) ? $end : $start;
    if ($start <= $today && $lastDay >= $today) {
        return 'today';
    }
    return $start > $today ? 'upcoming' : 'completed';
}

function content_public_events(): array
{
    $records = array_values(array_filter(content_read_records(EVENTS_RECORD_FILE), static fn (array $record): bool => !empty($record['visible']) && content_date_is_valid((string) ($record['start_date'] ?? ''))));
    usort($records, static function (array $left, array $right): int {
        $date = strcmp((string) ($left['start_date'] ?? ''), (string) ($right['start_date'] ?? ''));
        return $date !== 0 ? $date : strcmp((string) ($left['start_time'] ?? ''), (string) ($right['start_time'] ?? ''));
    });
    return $records;
}

function content_event_date_label(array $event): string
{
    $start = (string) ($event['start_date'] ?? '');
    $end = (string) ($event['end_date'] ?? '');
    $label = content_human_date($start);
    if ($end !== '' && $end !== $start) {
        $label .= ' – ' . content_human_date($end);
    }
    $startTime = content_human_time((string) ($event['start_time'] ?? ''));
    $endTime = content_human_time((string) ($event['end_time'] ?? ''));
    if ($startTime !== '') {
        $label .= ' · ' . $startTime . ($endTime !== '' ? '–' . $endTime : '');
    }
    return $label;
}

function content_share_url(string $url, string $text): string
{
    return 'https://wa.me/?text=' . rawurlencode(trim($text . ' ' . $url));
}

function content_absolute_url(string $path): string
{
    if (filter_var($path, FILTER_VALIDATE_URL) !== false) {
        return $path;
    }
    $secure = !empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off';
    $scheme = $secure ? 'https' : 'http';
    $host = preg_replace('/[^a-z0-9.\-:\[\]]/i', '', (string) ($_SERVER['HTTP_HOST'] ?? 'www.iyfinancesolutions.co.za'));
    return $scheme . '://' . ($host ?: 'www.iyfinancesolutions.co.za') . site_url($path);
}

function content_directions_url(array $event): string
{
    $destination = trim((string) ($event['address'] ?? '')) ?: trim((string) ($event['location'] ?? ''));
    return 'https://www.google.com/maps/dir/?api=1&destination=' . rawurlencode($destination);
}

function content_ics_escape(string $value): string
{
    return str_replace(["\\", ";", ",", "\r\n", "\r", "\n"], ["\\\\", "\\;", "\\,", "\\n", "\\n", "\\n"], $value);
}
