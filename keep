<?php
declare(strict_types=1);

require __DIR__ . '/config/bootstrap.php';
require PROJECT_ROOT . '/config/referrals.php';

start_admin_session();
$referralId = strtoupper(trim((string) ($_GET['ref'] ?? '')));

if (!preg_match('/^IYF-[0-9]{8}-[A-F0-9]{6}$/', $referralId)) {
    http_response_code(404);
    exit('This referral link could not be found.');
}

$referral = referral_find_by_id($referralId);
if ($referral === null || empty($referral['active'])) {
    http_response_code(404);
    exit('This referral link is no longer active. Please contact IY Finance Solutions.');
}

// Each valid link visit is logged and emailed. A visit is a scan/open event,
// not a verified unique person; link preview bots can also trigger a visit.
$scanId = 'scan-' . bin2hex(random_bytes(8));
$scannedAt = gmdate('c');
if (!referral_log_scan($referralId, $scanId, $scannedAt)) {
    error_log('IY referral scan could not be recorded for ' . $referralId . ' (' . $scanId . ').');
}
if (!referral_email_scan_notification($referral, $referralId, $scanId, $scannedAt)) {
    // The visitor still proceeds to the intended page; mail failures are logged server-side.
}
referral_store_attribution($referralId);

redirect_to('credit-assistance.php');
