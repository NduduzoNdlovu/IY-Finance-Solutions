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

// Do not store IP addresses or device fingerprints. A visit is a scan/open event,
// not a verified unique person; link preview bots can also trigger a visit.
if (!referral_log_scan($referralId)) {
    error_log('IY referral scan could not be recorded for ' . $referralId);
}
referral_store_attribution($referralId);

redirect_to('credit-assistance.php');
