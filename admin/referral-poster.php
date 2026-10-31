<?php
declare(strict_types=1);

require dirname(__DIR__) . '/config/bootstrap.php';
require PROJECT_ROOT . '/config/referrals.php';
require_admin();

$referralId = strtoupper(trim((string) ($_GET['id'] ?? '')));
$record = referral_find_by_id($referralId);
if ($record === null) {
    http_response_code(404);
    $adminTitle = 'Referral not found';
    require PROJECT_ROOT . '/includes/admin-header.php';
    echo '<section class="admin-shell"><div class="admin-card"><h1>Referral not found</h1><p>This referral ID could not be found.</p><a class="admin-button primary" href="' . h(site_url('admin/referrals.php')) . '">Return to referrals</a></div></section>';
    require PROJECT_ROOT . '/includes/admin-footer.php';
    exit;
}

$adminTitle = 'Print referral poster';
$adminPageStyles = ['css/referral-poster.css'];
$posterUrl = referral_public_url($referralId);
require PROJECT_ROOT . '/includes/admin-header.php';
?>

<section class="admin-shell poster-admin-toolbar">
    <div><span class="admin-eyebrow">Print-ready A4 poster</span><h1>Referral poster</h1><p>Confirm the QR code loads before printing. Use A4 paper and enable background graphics in the print dialog for the intended design.</p></div>
    <div class="poster-toolbar-actions">
        <a class="admin-button secondary" href="<?= h(site_url('admin/referrals.php')) ?>"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Back to referrals</a>
        <button class="admin-button primary" type="button" onclick="window.print()"><i class="fa-solid fa-print" aria-hidden="true"></i> Print poster</button>
    </div>
</section>

<main class="poster-preview-shell">
    <article class="iy-referral-poster" id="iy-referral-poster">
        <header class="poster-brand-row">
            <img src="<?= h(asset_url('images/logo/logoimg.webp')) ?>" alt="IY Finance Solutions logo" class="poster-logo">
            <div class="poster-brand-copy"><span>IY FINANCE</span><strong>SOLUTIONS</strong><small>A clearer path to financial freedom.</small></div>
            <div class="poster-brand-badge">CREDIT<br>ASSISTANCE</div>
        </header>

        <section class="poster-hero">
            <span class="poster-kicker">LET'S FIND A WAY FORWARD</span>
            <h2>Need help with<br><em>your credit profile?</em></h2>
            <p>Scan the QR code to contact IY Finance Solutions and discuss the credit-assistance options that may be available to you.</p>
        </section>

        <section class="poster-action-area">
            <div class="poster-qr-panel">
                <div class="poster-qr-frame"><div id="referral-qrcode" aria-label="QR code for this referral"></div></div>
                <strong>SCAN TO GET STARTED</strong>
                <span>Open your camera and point it at the QR code.</span>
                <small id="qr-status" role="status">Preparing QR code…</small>
            </div>
            <div class="poster-contact-panel">
                <span class="poster-kicker">YOUR REFERRAL CONTACT</span>
                <h3><?= h((string) $record['full_name']) ?></h3>
                <dl>
                    <div><dt>Place / area</dt><dd><?= h((string) $record['place']) ?></dd></div>
                    <div><dt>Work / organisation</dt><dd><?= h((string) $record['work']) ?></dd></div>
                    <?php if (trim((string) ($record['phone'] ?? '')) !== ''): ?><div><dt>Cellphone</dt><dd><?= h((string) $record['phone']) ?></dd></div><?php endif; ?>
                    <div><dt>Referral ID</dt><dd class="poster-referral-id"><?= h($referralId) ?></dd></div>
                </dl>
                <p class="poster-person-note">Mention this referral contact when enquiring so our team can assist you.</p>
            </div>
        </section>

        <section class="poster-benefits">
            <div><i class="fa-solid fa-comments" aria-hidden="true"></i><span><strong>Discuss your options</strong><small>Tell us about your credit concern.</small></span></div>
            <div><i class="fa-solid fa-user-shield" aria-hidden="true"></i><span><strong>Professional guidance</strong><small>Get information based on your circumstances.</small></span></div>
            <div><i class="fa-solid fa-clipboard-check" aria-hidden="true"></i><span><strong>Clear next steps</strong><small>Understand what you can do next.</small></span></div>
        </section>

        <footer class="poster-footer">
            <div><strong>IY FINANCE SOLUTIONS</strong><span>www.iyfinancesolutions.co.za</span><span>enquiries@iyfinancesolutions.co.za · 031 140 0378</span></div>
            <p>Services are subject to eligibility and applicable terms. An enquiry does not guarantee approval or a particular outcome.</p>
        </footer>
    </article>
</main>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js" referrerpolicy="no-referrer"></script>
<script>
(function () {
    const target = document.getElementById('referral-qrcode');
    const status = document.getElementById('qr-status');
    const url = <?= json_encode($posterUrl, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    if (typeof QRCode === 'undefined') {
        status.textContent = 'QR library did not load. Check your internet connection and reload this page before printing.';
        status.classList.add('qr-error');
        return;
    }
    new QRCode(target, {
        text: url,
        width: 220,
        height: 220,
        colorDark: '#073c2d',
        colorLight: '#ffffff',
        correctLevel: QRCode.CorrectLevel.H
    });
    status.textContent = 'QR code ready';
})();
</script>

<?php require PROJECT_ROOT . '/includes/admin-footer.php'; ?>
