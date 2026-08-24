<?php
declare(strict_types=1);

require __DIR__ . '/config/bootstrap.php';

const CONTACT_RECIPIENT = 'info@iyfinancesolutions.co.za';
const CONTACT_SENDER = 'info@iyfinancesolutions.co.za';
const CONTACT_RATE_LIMIT_SECONDS = 30;

function contact_input(string $key): string
{
    $value = $_POST[$key] ?? '';
    return is_string($value) ? trim($value) : '';
}

function contact_length(string $value): int
{
    return function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : strlen($value);
}

function contact_redirect(string $status, string $message, array $old = []): never
{
    start_admin_session();
    $_SESSION['contact_form_state'] = [
        'status' => $status,
        'message' => $message,
        'old' => $old,
    ];

    redirect_to('contact.php#contact-form');
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    redirect_to('contact.php#contact-form');
}

start_admin_session();

if (!csrf_is_valid(contact_input('csrf_token'))) {
    contact_redirect('error', 'Your form session expired. Please refresh the page and try again.');
}

$name = preg_replace('/\s+/u', ' ', contact_input('name')) ?? '';
$email = contact_input('email');
$message = preg_replace("/\r\n?|\r/", "\n", contact_input('message')) ?? '';
$honeypot = contact_input('company_website');
$old = ['name' => $name, 'email' => $email, 'message' => $message];

// Bots commonly complete this hidden field. Return a normal-looking success response.
if ($honeypot !== '') {
    contact_redirect('success', 'Thank you. Your message has been submitted.');
}

$lastSubmission = (int) ($_SESSION['contact_last_submission'] ?? 0);
if ($lastSubmission > 0 && (time() - $lastSubmission) < CONTACT_RATE_LIMIT_SECONDS) {
    contact_redirect('error', 'Please wait a moment before sending another message.', $old);
}

$errors = [];
if (contact_length($name) < 2 || contact_length($name) > 100) {
    $errors[] = 'Enter a valid full name.';
}
if (contact_length($email) > 254 || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
    $errors[] = 'Enter a valid email address.';
}
if (contact_length($message) < 10 || contact_length($message) > 4000) {
    $errors[] = 'Your message must contain between 10 and 4000 characters.';
}

if ($errors !== []) {
    contact_redirect('error', implode(' ', $errors), $old);
}

$safeName = str_replace(["\r", "\n"], ' ', $name);
$safeEmail = str_replace(["\r", "\n"], '', $email);
$submittedAt = date('Y-m-d H:i:s T');

$body = implode("\n", [
    'New website contact enquiry',
    '===========================',
    '',
    'Name: ' . $safeName,
    'Email: ' . $safeEmail,
    'Submitted: ' . $submittedAt,
    '',
    'Message:',
    $message,
    '',
    'Reply to this email to respond directly to ' . $safeName . '.',
]);

$headers = [
    'From: IY Finance Solutions Website <' . CONTACT_SENDER . '>',
    'Reply-To: ' . $safeEmail,
    'MIME-Version: 1.0',
    'Content-Type: text/plain; charset=UTF-8',
    'Content-Transfer-Encoding: 8bit',
    'X-Mailer: PHP/' . PHP_VERSION,
];

$sent = mail(
    CONTACT_RECIPIENT,
    'New website contact enquiry',
    wordwrap($body, 78),
    implode("\r\n", $headers)
);

if (!$sent) {
    contact_redirect(
        'error',
        'We could not send your message right now. Please email info@iyfinancesolutions.co.za directly or try again later.',
        $old
    );
}

$_SESSION['contact_last_submission'] = time();
contact_redirect('success', 'Thank you. Your message has been sent to the IY Finance Solutions team.');
