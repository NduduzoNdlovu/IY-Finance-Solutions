<?php
declare(strict_types=1);

require __DIR__ . '/config/bootstrap.php';
require PROJECT_ROOT . '/config/referrals.php';

start_admin_session();

/**
 * Display a branded IY Finance Solutions error page.
 */
function referral_error_alert(
    string $message,
    string $title = 'Referral Link Issue',
    int $statusCode = 404
): never {
    http_response_code($statusCode);

    $safeTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
    $safeMessage = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">

        <meta
            name="viewport"
            content="width=device-width, initial-scale=1.0"
        >

        <title>IY Finance Solutions</title>

        <style>
            :root {
                --iy-primary: #f58220;
                --iy-primary-dark: #d9650c;
                --iy-dark: #111827;
                --iy-text: #1f2937;
                --iy-muted: #6b7280;
                --iy-background: #f5f7fa;
                --iy-white: #ffffff;
                --iy-danger: #dc3545;
                --iy-danger-light: #fff1f2;
                --iy-border: #e5e7eb;
            }

            * {
                box-sizing: border-box;
                margin: 0;
                padding: 0;
            }

            body {
                min-height: 100vh;

                display: flex;
                align-items: center;
                justify-content: center;

                padding: 24px;

                font-family:
                    Inter,
                    -apple-system,
                    BlinkMacSystemFont,
                    "Segoe UI",
                    sans-serif;

                background:
                    radial-gradient(
                        circle at top right,
                        rgba(245, 130, 32, 0.10),
                        transparent 35%
                    ),
                    var(--iy-background);

                color: var(--iy-text);
            }

            .iy-error-wrapper {
                width: 100%;
                max-width: 480px;
            }

            .iy-error-card {
                background: var(--iy-white);

                border: 1px solid var(--iy-border);
                border-radius: 24px;

                padding: 38px 32px 32px;

                text-align: center;

                box-shadow:
                    0 20px 50px rgba(17, 24, 39, 0.10),
                    0 4px 12px rgba(17, 24, 39, 0.05);

                animation: iyCardIn 0.35s ease-out;
            }

            @keyframes iyCardIn {
                from {
                    opacity: 0;
                    transform: translateY(15px) scale(0.98);
                }

                to {
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }
            }

            .iy-brand {
                margin-bottom: 28px;
            }

            .iy-brand-name {
                font-size: 22px;
                font-weight: 800;
                letter-spacing: -0.5px;
                color: var(--iy-dark);
            }

            .iy-brand-name span {
                color: var(--iy-primary);
            }

            .iy-brand-subtitle {
                margin-top: 4px;

                font-size: 11px;
                font-weight: 700;

                letter-spacing: 1.5px;
                text-transform: uppercase;

                color: var(--iy-muted);
            }

            .iy-error-icon {
                width: 76px;
                height: 76px;

                margin: 0 auto 22px;

                display: flex;
                align-items: center;
                justify-content: center;

                border-radius: 50%;

                background: var(--iy-danger-light);
                color: var(--iy-danger);

                border: 8px solid rgba(220, 53, 69, 0.08);
            }

            .iy-error-icon svg {
                width: 36px;
                height: 36px;
            }

            .iy-error-title {
                font-size: 25px;
                line-height: 1.2;

                font-weight: 800;

                color: var(--iy-dark);

                margin-bottom: 12px;
            }

            .iy-error-message {
                max-width: 390px;

                margin: 0 auto;

                font-size: 15px;
                line-height: 1.65;

                color: var(--iy-muted);
            }

            .iy-error-actions {
                display: flex;
                flex-direction: column;

                gap: 10px;

                margin-top: 28px;
            }

            .iy-button {
                width: 100%;
                min-height: 48px;

                display: flex;
                align-items: center;
                justify-content: center;

                border-radius: 12px;

                padding: 12px 18px;

                text-decoration: none;

                font-size: 14px;
                font-weight: 700;

                transition:
                    transform 0.2s ease,
                    box-shadow 0.2s ease,
                    background 0.2s ease;
            }

            .iy-button-primary {
                background: var(--iy-primary);
                color: #ffffff;

                box-shadow:
                    0 8px 18px rgba(245, 130, 32, 0.25);
            }

            .iy-button-primary:hover {
                background: var(--iy-primary-dark);

                transform: translateY(-1px);

                box-shadow:
                    0 10px 22px rgba(245, 130, 32, 0.30);
            }

            .iy-button-secondary {
                background: #f9fafb;

                color: var(--iy-dark);

                border: 1px solid var(--iy-border);
            }

            .iy-button-secondary:hover {
                background: #f3f4f6;

                transform: translateY(-1px);
            }

            .iy-error-footer {
                margin-top: 25px;

                padding-top: 20px;

                border-top: 1px solid var(--iy-border);

                font-size: 12px;
                line-height: 1.5;

                color: var(--iy-muted);
            }

            .iy-error-footer strong {
                color: var(--iy-dark);
            }

            @media (max-width: 480px) {
                body {
                    padding: 16px;
                }

                .iy-error-card {
                    padding: 30px 22px 25px;
                    border-radius: 20px;
                }

                .iy-error-title {
                    font-size: 22px;
                }

                .iy-error-message {
                    font-size: 14px;
                }
            }
        </style>
    </head>

    <body>

        <main class="iy-error-wrapper">

            <section class="iy-error-card">

                <div class="iy-brand">
                    <div class="iy-brand-name">
                        IY <span>Finance Solutions</span>
                    </div>

                    <div class="iy-brand-subtitle">
                        Financial Solutions You Can Trust
                    </div>
                </div>

                <div class="iy-error-icon" aria-hidden="true">
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="M10.3 3.9L2.2 18a2 2 0 0 0 1.7 3h16.2a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0Z"/>
                        <line x1="12" y1="9" x2="12" y2="13"/>
                        <line x1="12" y1="17" x2="12.01" y2="17"/>
                    </svg>
                </div>

                <h1 class="iy-error-title">
                    <?= $safeTitle ?>
                </h1>

                <p class="iy-error-message">
                    <?= $safeMessage ?>
                </p>

                <div class="iy-error-actions">

                    <a
                        href="credit-assistance.php"
                        class="iy-button iy-button-primary"
                    >
                        Continue to Credit Assistance
                    </a>

                    <a
                        href="javascript:history.back()"
                        class="iy-button iy-button-secondary"
                    >
                        Go Back
                    </a>

                </div>

                <div class="iy-error-footer">
                    Need help?
                    <strong>Contact IY Finance Solutions</strong>
                    and we'll be happy to assist you.
                </div>

            </section>

        </main>

    </body>
    </html>
    <?php

    exit;
}

$referralId = strtoupper(
    trim((string) ($_GET['ref'] ?? ''))
);

/*
 * Validate referral ID.
 */
if (!preg_match('/^IYF-[0-9]{8}-[A-F0-9]{6}$/', $referralId)) {
    referral_error_alert(
        'The referral link you used is invalid or incomplete. Please check the link and try again.',
        'Invalid Referral Link'
    );
}

/*
 * Find the referral.
 */
$referral = referral_find_by_id($referralId);

if ($referral === null) {
    referral_error_alert(
        'We could not find this referral link. Please contact IY Finance Solutions if you believe this is an error.',
        'Referral Link Not Found'
    );
}

/*
 * Check whether the referral is active.
 */
if (empty($referral['active'])) {
    referral_error_alert(
        'This referral link is no longer active. Please contact IY Finance Solutions for assistance.',
        'Referral Link Inactive'
    );
}

/*
 * Each valid link visit is logged and emailed.
 *
 * A visit is a scan/open event, not a verified unique person.
 * Link preview bots can also trigger a visit.
 */
$scanId = 'scan-' . bin2hex(random_bytes(8));

$scannedAt = gmdate('c');

if (!referral_log_scan(
    $referralId,
    $scanId,
    $scannedAt,
    $referral
)) {
    error_log(
        'IY referral scan could not be recorded for '
        . $referralId
        . ' ('
        . $scanId
        . ').'
    );
}

/*
 * Send scan notification email.
 *
 * Mail failures are logged internally.
 * The visitor should still continue to the intended page.
 */
if (!referral_email_scan_notification(
    $referral,
    $referralId,
    $scanId,
    $scannedAt,
    $referral
)) {
    error_log(
        'IY referral scan notification email could not be sent for '
        . $referralId
        . ' ('
        . $scanId
        . ').'
    );
}

/*
 * Store referral attribution.
 */
referral_store_attribution($referralId);

/*
 * Send visitor to Credit Assistance.
 */
redirect_to('credit-assistance.php?ref=' . rawurlencode($referralId));
