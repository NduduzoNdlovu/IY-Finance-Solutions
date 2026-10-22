<?php
declare(strict_types=1);

require dirname(__DIR__) . '/config/bootstrap.php';
require PROJECT_ROOT . '/config/client-feedback.php';
require_admin();

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    redirect_to('admin/feedback.php');
}
if (!csrf_is_valid($_POST['csrf_token'] ?? null)) {
    flash_message('error', 'Your session expired. Please try again.');
    redirect_to('admin/feedback.php');
}

$id = trim((string) ($_POST['id'] ?? ''));
$action = trim((string) ($_POST['action'] ?? ''));
if ($id === '' || !in_array($action, ['approve', 'reject', 'delete'], true)) {
    flash_message('error', 'The feedback action was not valid.');
    redirect_to('admin/feedback.php');
}

$found = false;
$consentBlocked = false;
$updated = client_feedback_mutate(static function (array &$records) use ($id, $action, &$found, &$consentBlocked): bool {
    foreach ($records as $index => &$record) {
        if (($record['id'] ?? '') !== $id) {
            continue;
        }
        $found = true;
        if ($action === 'delete') {
            unset($records[$index]);
            $records = array_values($records);
            return true;
        }
        if ($action === 'approve' && empty($record['publication_consent'])) {
            $consentBlocked = true;
            return false;
        }
        $record['status'] = $action === 'approve' ? 'approved' : 'rejected';
        $record['reviewed_at'] = gmdate('c');
        $record['reviewed_by'] = (string) ($_SESSION['admin_username'] ?? 'administrator');
        return true;
    }
    unset($record);
    return false;
});

if ($consentBlocked) {
    flash_message('error', 'This feedback cannot be published because the client has not given publication permission.');
} elseif (!$found) {
    flash_message('error', 'The feedback record could not be found.');
} elseif (!$updated) {
    flash_message('error', 'The feedback could not be updated. Please try again.');
} else {
    $message = match ($action) {
        'approve' => 'Feedback approved and published.',
        'reject' => 'Feedback rejected or unpublished. It will no longer appear publicly.',
        'delete' => 'Feedback record deleted.',
    };
    flash_message('success', $message);
}
redirect_to('admin/feedback.php');
