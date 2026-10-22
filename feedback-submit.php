<?php
declare(strict_types=1);

require __DIR__ . '/config/bootstrap.php';
require PROJECT_ROOT . '/config/client-feedback.php';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    redirect_to('feedback.php');
}

start_admin_session();
$old = [
    'service' => trim((string) ($_POST['service'] ?? '')),
    'language' => trim((string) ($_POST['language'] ?? '')),
    'name' => trim((string) ($_POST['name'] ?? '')),
    'email' => trim((string) ($_POST['email'] ?? '')),
    'rating' => trim((string) ($_POST['rating'] ?? '')),
    'feedback' => trim((string) ($_POST['feedback'] ?? '')),
    'privacy_acknowledged' => !empty($_POST['privacy_acknowledged']),
    'publication_consent' => !empty($_POST['publication_consent']),
    'display_name_consent' => !empty($_POST['display_name_consent']),
];

$fail = static function (string $message) use ($old): never {
    $_SESSION['client_feedback_state'] = ['status' => 'error', 'message' => $message, 'old' => $old];
    redirect_to('feedback.php');
};

if (!csrf_is_valid($_POST['csrf_token'] ?? null)) {
    $fail('Your form session expired. Please refresh the page and try again.');
}

// Quietly discard automated submissions caught by the hidden honeypot.
if (trim((string) ($_POST['company_website'] ?? '')) !== '') {
    $_SESSION['client_feedback_state'] = ['status' => 'success', 'message' => 'Thank you. Your feedback has been received.', 'old' => []];
    redirect_to('feedback.php');
}

$startedAt = filter_var($_POST['form_started_at'] ?? null, FILTER_VALIDATE_INT);
if ($startedAt === false || $startedAt === null || time() - (int) $startedAt < 2 || time() - (int) $startedAt > 14400) {
    $fail('Please take a moment to complete the form, then submit it again.');
}

$now = time();
$submissionTimes = array_values(array_filter(
    is_array($_SESSION['client_feedback_submission_times'] ?? null) ? $_SESSION['client_feedback_submission_times'] : [],
    static fn ($stamp): bool => is_int($stamp) && $stamp > $now - 3600
));
if (count($submissionTimes) >= 3) {
    $fail('You have submitted feedback several times recently. Please try again later.');
}

$allowedServices = client_feedback_services();
if ($old['service'] !== '' && !in_array($old['service'], $allowedServices, true)) {
    $fail('Please select a valid service.');
}
if (client_feedback_text_length($old['language']) > 60 || client_feedback_text_length($old['name']) > 100) {
    $fail('Please check the length of the language or name field.');
}
if ($old['email'] !== '' && (strlen($old['email']) > 254 || filter_var($old['email'], FILTER_VALIDATE_EMAIL) === false)) {
    $fail('Please enter a valid email address, or leave the email field blank.');
}
if ($old['rating'] !== '' && !in_array($old['rating'], ['1', '2', '3', '4', '5'], true)) {
    $fail('Please select a valid rating or leave it blank.');
}
if (client_feedback_text_length($old['feedback']) < 10 || client_feedback_text_length($old['feedback']) > 3000) {
    $fail('Your feedback must contain between 10 and 3,000 characters.');
}
if (!$old['privacy_acknowledged']) {
    $fail('Please acknowledge the privacy information before submitting your feedback.');
}

$record = [
    'id' => content_new_id('feedback'),
    'service' => $old['service'],
    'language' => $old['language'],
    'name' => $old['name'],
    'email' => $old['email'],
    'rating' => $old['rating'] === '' ? null : (int) $old['rating'],
    'feedback' => $old['feedback'],
    'privacy_acknowledged' => true,
    'privacy_acknowledged_at' => gmdate('c'),
    'publication_consent' => $old['publication_consent'],
    'publication_consent_at' => $old['publication_consent'] ? gmdate('c') : null,
    'display_name_consent' => $old['publication_consent'] && $old['display_name_consent'] && $old['name'] !== '',
    'status' => 'pending',
    'created_at' => gmdate('c'),
    'reviewed_at' => null,
    'reviewed_by' => null,
];

$saved = client_feedback_mutate(static function (array &$records) use ($record): bool {
    $records[] = $record;
    return true;
});

if (!$saved) {
    $fail('We could not save your feedback just now. Please try again later.');
}

$submissionTimes[] = $now;
$_SESSION['client_feedback_submission_times'] = $submissionTimes;
$_SESSION['client_feedback_state'] = [
    'status' => 'success',
    'message' => 'Thank you for sharing your experience. Your feedback has been received and will be reviewed privately.',
    'old' => [],
];
redirect_to('feedback.php');
