<?php
declare(strict_types=1);

if (!defined('PROJECT_ROOT') || !defined('STORAGE_DIRECTORY')) {
    throw new RuntimeException('Load config/bootstrap.php before config/referrals.php.');
}

defined('REFERRALS_FILE') || define('REFERRALS_FILE', STORAGE_DIRECTORY . '/referrals.json');
defined('REFERRAL_SCANS_FILE') || define('REFERRAL_SCANS_FILE', STORAGE_DIRECTORY . '/referral-scans.json');
defined('REFERRAL_ATTRIBUTION_COOKIE') || define('REFERRAL_ATTRIBUTION_COOKIE', 'iy_referral_id');
defined('REFERRAL_ATTRIBUTION_DAYS') || define('REFERRAL_ATTRIBUTION_DAYS', 30);
defined('IY_CANONICAL_URL') || define('IY_CANONICAL_URL', 'https://iyfinancesolutions.co.za');

function referral_read_records(string $file): array
{
    if (!is_file($file)) {
        return [];
    }

    $lock = @fopen($file . '.lock', 'c');
    if ($lock === false) {
        $json = @file_get_contents($file);
    } else {
        flock($lock, LOCK_SH);
        $json = @file_get_contents($file);
        flock($lock, LOCK_UN);
        fclose($lock);
    }

    if (!is_string($json) || trim($json) === '') {
        return [];
    }

    $records = json_decode($json, true);
    return is_array($records)
        ? array_values(array_filter($records, 'is_array'))
        : [];
}

/**
 * Serialize read-modify-write operations using a separate lock file.
 * The temporary file + rename prevents readers from seeing partial JSON.
 */
function referral_update_records(string $file, callable $mutator): bool
{
    $directory = dirname($file);
    if (!is_dir($directory) && !mkdir($directory, 0750, true) && !is_dir($directory)) {
        return false;
    }

    $lock = @fopen($file . '.lock', 'c');
    if ($lock === false) {
        return false;
    }

    try {
        if (!flock($lock, LOCK_EX)) {
            return false;
        }

        $current = [];
        if (is_file($file)) {
            $raw = file_get_contents($file);
            if ($raw === false) {
                return false;
            }
            if (trim($raw) !== '') {
                $decoded = json_decode($raw, true);
                if (!is_array($decoded)) {
                    return false;
                }
                $current = array_values(array_filter($decoded, 'is_array'));
            }
        }

        $updated = $mutator($current);
        if (!is_array($updated)) {
            return false;
        }

        $json = json_encode(array_values($updated), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if (!is_string($json)) {
            return false;
        }

        try {
            $suffix = bin2hex(random_bytes(6));
        } catch (Throwable $exception) {
            $suffix = str_replace('.', '', uniqid('', true));
        }

        $temporaryFile = $file . '.tmp-' . $suffix;
        if (file_put_contents($temporaryFile, $json . PHP_EOL, LOCK_EX) === false) {
            return false;
        }
        @chmod($temporaryFile, 0640);

        if (DIRECTORY_SEPARATOR === '\\' && is_file($file) && !@unlink($file)) {
            @unlink($temporaryFile);
            return false;
        }
        if (!rename($temporaryFile, $file)) {
            @unlink($temporaryFile);
            return false;
        }
        @chmod($file, 0640);
        return true;
    } finally {
        flock($lock, LOCK_UN);
        fclose($lock);
    }
}

function referral_find_by_id(string $referralId): ?array
{
    foreach (referral_read_records(REFERRALS_FILE) as $record) {
        if (hash_equals((string) ($record['referral_id'] ?? ''), $referralId)) {
            return $record;
        }
    }
    return null;
}

function referral_public_url(string $referralId): string
{
    $basePath = base_path();
    return rtrim(IY_CANONICAL_URL, '/') . $basePath . '/r/' . rawurlencode($referralId);
}

function referral_current_id(): string
{
    start_admin_session();
    $candidate = (string) ($_SESSION['iy_referral_id'] ?? $_COOKIE[REFERRAL_ATTRIBUTION_COOKIE] ?? '');
    if ($candidate === '' || !preg_match('/^IYF-[0-9]{8}-[A-F0-9]{6}$/', $candidate)) {
        return '';
    }

    $record = referral_find_by_id($candidate);
    return $record !== null && !empty($record['active']) ? $candidate : '';
}

function referral_store_attribution(string $referralId): void
{
    start_admin_session();
    $_SESSION['iy_referral_id'] = $referralId;
    $secure = !empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off';
    setcookie(REFERRAL_ATTRIBUTION_COOKIE, $referralId, [
        'expires' => time() + (REFERRAL_ATTRIBUTION_DAYS * 86400),
        'path' => base_path() === '' ? '/' : base_path() . '/',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

function referral_log_scan(string $referralId, ?string $scanId = null, ?string $scannedAt = null, ?array $referral = null): bool
{
    $scanId ??= 'scan-' . bin2hex(random_bytes(8));
    $scannedAt ??= gmdate('c');

    return referral_update_records(REFERRAL_SCANS_FILE, static function (array $records) use ($referralId, $scanId, $scannedAt, $referral): array {
        $records[] = [
            'id' => $scanId,
            'referral_id' => $referralId,
            'referral_person' => (string) ($referral['full_name'] ?? ''),
            'scanned_at' => $scannedAt,
        ];
        return $records;
    });
}

/** Send an email for every valid referral-link visit. */
function referral_email_scan_notification(array $referral, string $referralId, string $scanId, string $scannedAt): bool
{
    $recipient = 'enquiries@iyfinancesolutions.co.za';
    $sender = 'enquiries@iyfinancesolutions.co.za';
    $safeReferralId = preg_replace('/[^A-Z0-9-]/', '', strtoupper($referralId)) ?? '';

    try {
        $utcTime = new DateTimeImmutable($scannedAt);
        $localTime = $utcTime->setTimezone(new DateTimeZone('Africa/Johannesburg'))->format('d M Y, H:i:s') . ' SAST';
    } catch (Throwable $exception) {
        $localTime = date('d M Y, H:i:s') . ' (server time)';
    }

    $referralUrl = referral_public_url($referralId);
    $destinationUrl = rtrim(IY_CANONICAL_URL, '/') . base_path() . '/credit-assistance.php';
    $body = implode("\n", [
        'A QR referral link was opened on the IY Finance Solutions website.',
        '',
        'SCAN DETAILS',
        'Scan event ID: ' . $scanId,
        'Scanned at: ' . $localTime,
        'Referral ID: ' . $safeReferralId,
        'Referral URL: ' . $referralUrl,
        'Destination page: ' . $destinationUrl,
        '',
        'REFERRAL PERSON DETAILS',
        'Full name: ' . (string) ($referral['full_name'] ?? 'Not available'),
        'Place / area: ' . (string) ($referral['place'] ?? 'Not available'),
        'Work / role / organisation: ' . (string) ($referral['work'] ?? 'Not available'),
        '',
        'This notification records a QR link-open event, not a verified unique person or a submitted enquiry.',
        'IP addresses and device fingerprints are not collected by this feature.',
    ]);

    $headers = [
        'From: IY Finance Solutions Website <' . $sender . '>',
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=UTF-8',
        'Content-Transfer-Encoding: 8bit',
        'X-Mailer: PHP/' . PHP_VERSION,
    ];

    $sent = @mail(
        $recipient,
        'QR referral scan: ' . $safeReferralId,
        wordwrap($body, 78),
        implode("\r\n", $headers)
    );

    if (!$sent) {
        error_log('IY referral scan email could not be sent for ' . $safeReferralId . ' (' . $scanId . '). Check the hosting mail configuration.');
    }

    return $sent;
}


function referral_append_record(string $file, array $record): bool
{
    return referral_update_records($file, static function (array $records) use ($record): array {
        $records[] = $record;
        return $records;
    });
}

function referral_new_id(): string
{
    return 'IYF-' . gmdate('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
}
