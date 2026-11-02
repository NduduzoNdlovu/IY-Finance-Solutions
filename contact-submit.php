<?php
declare(strict_types=1);

require __DIR__ . '/config/bootstrap.php';

const CONTACT_RECIPIENT = 'enquiries@iyfinancesolutions.co.za';
const CONTACT_SENDER = 'enquiries@iyfinancesolutions.co.za';
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
$contact_number = contact_input('contact_number');
$email = contact_input('email');
$location = contact_input('location');
$message = preg_replace("/\r\n?|\r/", "\n", contact_input('message')) ?? '';
$honeypot = contact_input('company_website');

$old = [
    'name' => $name,
    'contact_number' => $contact_number,
    'email' => $email,
    'location' => $location,
    'message' => $message
];

// Bots commonly complete this hidden field.
// Return a normal-looking success response.
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

if (contact_length($contact_number) < 10 || contact_length($contact_number) > 20) {
    $errors[] = 'Enter a valid contact number.';
}

if (contact_length($email) > 254 || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
    $errors[] = 'Enter a valid email address.';
}

if (contact_length($location) > 200) {
    $errors[] = 'Enter a valid postal address.';
}

if (contact_length($message) < 10 || contact_length($message) > 4000) {
    $errors[] = 'Your message must contain between 10 and 4000 characters.';
}

if ($errors !== []) {
    contact_redirect('error', implode(' ', $errors), $old);
}

$safeName = str_replace(["\r", "\n"], ' ', $name);
$safeEmail = str_replace(["\r", "\n"], '', $email);
$safeContactNumber = str_replace(["\r", "\n"], '', $contact_number);

$submittedAt = date('Y-m-d H:i:s T');

// Escape user-submitted information for HTML emails.
$htmlName = htmlspecialchars($safeName, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$htmlContactNumber = htmlspecialchars($safeContactNumber, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$htmlEmail = htmlspecialchars($safeEmail, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$htmlLocation = htmlspecialchars($location, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$htmlMessage = nl2br(htmlspecialchars($message, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));
$htmlSubmittedAt = htmlspecialchars($submittedAt, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

// Professional HTML email design.
$body = '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Website Contact Enquiry</title>
</head>

<body style="margin:0; padding:0; background-color:#f3f6f4; font-family:Arial,Helvetica,sans-serif; color:#333333;">

<table width="100%" cellpadding="0" cellspacing="0" border="0"
       style="background-color:#f3f6f4; padding:30px 10px;">

<tr>
<td align="center">

<table width="600" cellpadding="0" cellspacing="0" border="0"
       style="width:100%; max-width:600px; background-color:#ffffff; border-radius:10px; overflow:hidden;">

<!-- HEADER -->
<tr>
<td align="center"
    style="background-color:#176b45; padding:35px 25px;">

    <h1 style="margin:0; color:#ffffff; font-size:26px; font-weight:bold;">
        IY Finance Solutions
    </h1>

    <p style="margin:10px 0 0; color:#e4f3e9; font-size:14px;">
        Finance solutions that will give you financial freedom.
    </p>

</td>
</tr>

<!-- TITLE -->
<tr>
<td style="padding:30px 30px 15px;">

    <h2 style="margin:0; color:#176b45; font-size:22px;">
        New Website Contact Enquiry
    </h2>

    <p style="margin:12px 0 0; font-size:14px; line-height:1.7; color:#555555;">
        You have received a new enquiry through the IY Finance Solutions website.
        Please review the information below and contact the client.
    </p>

</td>
</tr>

<!-- CONTACT INFORMATION -->
<tr>
<td style="padding:15px 30px;">

    <h3 style="margin:0 0 15px; font-size:16px; color:#176b45;">
        Contact Information
    </h3>

    <table width="100%" cellpadding="0" cellspacing="0" border="0"
           style="border:1px solid #e2e8e4; border-radius:6px;">

        <tr>
            <td style="padding:12px; background-color:#f6faf7; width:35%; font-size:13px; font-weight:bold; color:#555555;">
                Full Name
            </td>
            <td style="padding:12px; font-size:14px; color:#333333;">
                ' . $htmlName . '
            </td>
        </tr>

        <tr>
            <td style="padding:12px; background-color:#f6faf7; font-size:13px; font-weight:bold; color:#555555;">
                Contact Number
            </td>
            <td style="padding:12px; font-size:14px; color:#333333;">
                ' . $htmlContactNumber . '
            </td>
        </tr>

        <tr>
            <td style="padding:12px; background-color:#f6faf7; font-size:13px; font-weight:bold; color:#555555;">
                Email Address
            </td>
            <td style="padding:12px; font-size:14px; color:#333333; word-break:break-word;">
                ' . $htmlEmail . '
            </td>
        </tr>

        <tr>
            <td style="padding:12px; background-color:#f6faf7; font-size:13px; font-weight:bold; color:#555555;">
                Location
            </td>
            <td style="padding:12px; font-size:14px; color:#333333;">
                ' . $htmlLocation . '
            </td>
        </tr>

        <tr>
            <td style="padding:12px; background-color:#f6faf7; font-size:13px; font-weight:bold; color:#555555;">
                Submitted
            </td>
            <td style="padding:12px; font-size:14px; color:#333333;">
                ' . $htmlSubmittedAt . '
            </td>
        </tr>

    </table>

</td>
</tr>

<!-- MESSAGE -->
<tr>
<td style="padding:15px 30px 30px;">

    <h3 style="margin:0 0 15px; font-size:16px; color:#176b45;">
        Client Message
    </h3>

    <div style="background-color:#f6faf7; border-left:4px solid #176b45; padding:20px; font-size:14px; line-height:1.8; color:#333333; border-radius:4px;">
        ' . $htmlMessage . '
    </div>

</td>
</tr>

<!-- REPLY NOTICE -->
<tr>
<td style="padding:0 30px 30px;">

    <p style="margin:0; font-size:13px; line-height:1.7; color:#555555;">
        <strong>Important:</strong> You can reply directly to this email
        to respond to the client.
    </p>

</td>
</tr>

<!-- FOOTER -->
<tr>
<td align="center"
    style="background-color:#176b45; padding:25px;">

    <p style="margin:0; font-size:14px; font-weight:bold; color:#ffffff;">
        IY Finance Solutions
    </p>

    <p style="margin:10px 0; font-size:12px; color:#e4f3e9;">
        Finance solutions that will give you financial freedom.
    </p>

    <p style="margin:0; font-size:12px; color:#ffffff;">
        enquiries@iyfinancesolutions.co.za
    </p>

</td>
</tr>

</table>

</td>
</tr>

</table>

</body>
</html>
';

// Email headers.
$headers = [
    'From: IY Finance Solutions Website <' . CONTACT_SENDER . '>',
    'Reply-To: ' . $safeEmail,
    'MIME-Version: 1.0',
    'Content-Type: text/html; charset=UTF-8',
    'Content-Transfer-Encoding: 8bit',
    'X-Mailer: PHP/' . PHP_VERSION,
];

$sent = mail(
    CONTACT_RECIPIENT,
    'New website contact enquiry',
    $body,
    implode("\r\n", $headers)
);

if (!$sent) {
    contact_redirect(
        'error',
        'We could not send your message right now. Please email enquiries@iyfinancesolutions.co.za directly or try again later.',
        $old
    );
}

$_SESSION['contact_last_submission'] = time();

contact_redirect(
    'success',
    'Thank you. Your message has been sent to the IY Finance Solutions team.'
);
