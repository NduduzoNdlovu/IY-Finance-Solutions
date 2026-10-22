<?php
declare(strict_types=1);

if (!defined('PROJECT_ROOT') || !defined('STORAGE_DIRECTORY')) {
    throw new RuntimeException('Load config/bootstrap.php before config/client-feedback.php.');
}

require_once PROJECT_ROOT . '/config/content.php';

defined('CLIENT_FEEDBACK_FILE') || define('CLIENT_FEEDBACK_FILE', STORAGE_DIRECTORY . '/client-feedback.json');
defined('CLIENT_FEEDBACK_LOCK_FILE') || define('CLIENT_FEEDBACK_LOCK_FILE', STORAGE_DIRECTORY . '/client-feedback.lock');

function client_feedback_services(): array
{
    return [
        'Credit Clearance',
        'Default (Adverse Listing)',
        'Account Negotiations',
        'Judgements',
        'Disputes (Updating)',
        'Administrator Order',
        'Debt Review Removal',
        'Financial Assessment',
        'Funeral Cover',
        'Personal Loans',
        'Other service',
    ];
}

/** Read all feedback records. This file must remain inside the protected storage directory. */
function client_feedback_read(): array
{
    return content_read_records(CLIENT_FEEDBACK_FILE);
}

/**
 * Perform a read/modify/write while holding a separate lock, preventing two submissions
 * or moderation actions from overwriting each other's JSON records.
 */
function client_feedback_mutate(callable $mutator): bool
{
    if (!is_dir(STORAGE_DIRECTORY) && !mkdir(STORAGE_DIRECTORY, 0750, true) && !is_dir(STORAGE_DIRECTORY)) {
        return false;
    }

    $lock = @fopen(CLIENT_FEEDBACK_LOCK_FILE, 'c');
    if ($lock === false) {
        return false;
    }

    try {
        if (!flock($lock, LOCK_EX)) {
            return false;
        }

        $records = client_feedback_read();
        $changed = $mutator($records);
        if ($changed !== true) {
            return false;
        }

        return content_write_records(CLIENT_FEEDBACK_FILE, $records);
    } finally {
        @flock($lock, LOCK_UN);
        @fclose($lock);
    }
}

function client_feedback_text_length(string $value): int
{
    return function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : strlen($value);
}

function client_feedback_excerpt(string $value, int $limit = 180): string
{
    $value = trim(preg_replace('/\s+/u', ' ', $value) ?? $value);
    if (client_feedback_text_length($value) <= $limit) {
        return $value;
    }
    $short = function_exists('mb_substr') ? mb_substr($value, 0, $limit - 1, 'UTF-8') : substr($value, 0, $limit - 1);
    return rtrim($short) . '…';
}
