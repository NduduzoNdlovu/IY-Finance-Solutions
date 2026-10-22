<?php
declare(strict_types=1);

require __DIR__ . '/config/bootstrap.php';
require PROJECT_ROOT . '/config/content.php';
require PROJECT_ROOT . '/config/referrals.php';
start_admin_session();

$pageTitle = 'Credit Assistance | IY Finance Solutions';
$pageDescription = 'Were you declined for credit? Tell IY Finance Solutions what you were trying to purchase or apply for and our team will contact you.';
$activePage = 'credit-assistance';
$bodyClass = 'credit-assistance-page';
$pageStyles = ['css/credit-assistance.css'];

$errors = [];
$success = false;
$submitted = [
    'first_name' => '', 'surname' => '', 'phone' => '', 'email' => '',
    'credit_type' => '', 'decline_reason' => '', 'message' => '',
];

$creditTypes = [
    'vehicle' => 'Vehicle finance', 'personal-loan' => 'Personal loan',
    'home-loan' => 'Home loan', 'furniture' => 'Furniture / retail credit',
    'cellphone' => 'Cellphone / contract', 'credit-card' => 'Credit card', 'other' => 'Other',
];

$declineReasons = [
    'declined' => 'My application was declined',
    'credit-profile' => 'I was told there is an issue with my credit profile',
    'unsure' => 'I am not sure why I was declined', 'other' => 'Other',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($submitted as $field => $value) {
        $submitted[$field] = trim((string) ($_POST[$field] ?? ''));
    }

    if (trim((string) ($_POST['website'] ?? '')) !== '') {
        redirect_to('credit-assistance.php');
    }

    if ($submitted['first_name'] === '') $errors[] = 'Please enter your first name.';
    if ($submitted['surname'] === '') $errors[] = 'Please enter your surname.';
    if ($submitted['phone'] === '') {
        $errors[] = 'Please enter your cellphone number.';
    } elseif (!preg_match('/^[0-9+()\s-]{7,25}$/', $submitted['phone'])) {
        $errors[] = 'Please enter a valid cellphone number.';
    }
    if ($submitted['email'] !== '' && filter_var($submitted['email'], FILTER_VALIDATE_EMAIL) === false) {
        $errors[] = 'Please enter a valid email address or leave it blank.';
    }
    if (!isset($creditTypes[$submitted['credit_type']])) {
        $errors[] = 'Please select what you were trying to purchase or apply for.';
    }
    if (!isset($declineReasons[$submitted['decline_reason']])) {
        $errors[] = 'Please select what happened with your application.';
    }
    if (empty($_POST['consent'])) {
        $errors[] = 'Please confirm that IY Finance Solutions may contact you about your enquiry.';
    }

    if ($errors === []) {
        $referralId = referral_current_id();
        $referralRecord = $referralId !== '' ? referral_find_by_id($referralId) : null;
        $newEnquiry = [
            'id' => content_new_id('credit'), 'created_at' => gmdate('c'),
            'first_name' => $submitted['first_name'], 'surname' => $submitted['surname'],
            'phone' => $submitted['phone'], 'email' => $submitted['email'],
            'credit_type' => $submitted['credit_type'],
            'credit_type_label' => $creditTypes[$submitted['credit_type']],
            'decline_reason' => $submitted['decline_reason'],
            'decline_reason_label' => $declineReasons[$submitted['decline_reason']],
            'message' => $submitted['message'], 'status' => 'new',
            'referral_id' => $referralRecord !== null ? $referralId : '',
            'referral_person' => $referralRecord !== null ? (string) ($referralRecord['full_name'] ?? '') : '',
        ];

        if (referral_append_record(CREDIT_ENQUIRIES_FILE, $newEnquiry)) {
            $success = true;
            $submitted = array_fill_keys(array_keys($submitted), '');
        } else {
            $errors[] = 'We could not save your enquiry right now. Please try again or contact IY Finance Solutions directly.';
        }
    }
}

require PROJECT_ROOT . '/includes/header.php';
?>

<section class="credit-assistance-main">
    <div class="shell credit-assistance-main-inner">
        <nav class="breadcrumbs" aria-label="Breadcrumb">
            <a href="<?= h(site_url('index.php')) ?>">Home</a>
            <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
            <span>Credit assistance</span>
        </nav>

        <div class="credit-assistance-layout">
            <div class="credit-assistance-intro">
                <span class="eyebrow light">Need help after a declined application?</span>
                <h1>Let's see how we can help.</h1>
                <p>Were you declined for credit? Complete this short form and tell us what you were trying to purchase or apply for. An IY Finance Solutions consultant will contact you.</p>
                <div class="credit-assistance-points">
                    <div><i class="fa-solid fa-phone-volume" aria-hidden="true"></i><span><strong>We contact you</strong><small>A consultant will call using the details you provide.</small></span></div>
                    <div><i class="fa-solid fa-lock" aria-hidden="true"></i><span><strong>Keep it simple</strong><small>No credit report upload is required on this form.</small></span></div>
                    <div><i class="fa-solid fa-comments" aria-hidden="true"></i><span><strong>Discuss your options</strong><small>Tell us what happened and we can guide the conversation.</small></span></div>
                </div>
            </div>

            <div class="credit-assistance-card">
                <?php if ($success): ?>
                    <div class="credit-success" role="status">
                        <span class="credit-success-icon"><i class="fa-solid fa-check" aria-hidden="true"></i></span>
                        <span class="eyebrow">Enquiry received</span>
                        <h2>Thank you. We have your details.</h2>
                        <p>An IY Finance Solutions consultant will contact you using the details you provided.</p>
                        <a class="button button-primary" href="<?= h(site_url('index.php')) ?>"><i class="fa-solid fa-house" aria-hidden="true"></i> Back to home</a>
                    </div>
                <?php else: ?>
                    <?php if ($errors !== []): ?>
                        <div class="credit-errors" role="alert"><strong>Please check the following:</strong><ul><?php foreach ($errors as $error): ?><li><?= h($error) ?></li><?php endforeach; ?></ul></div>
                    <?php endif; ?>

                    <form class="credit-form" method="post" action="<?= h(site_url('credit-assistance.php')) ?>">
                        <input class="credit-honeypot" type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true">
                        <div class="credit-form-heading"><span class="eyebrow">Get started</span><h2>Tell us about your enquiry</h2><p>Fields marked with * are required.</p></div>
                        <div class="credit-form-grid">
                            <label><span>First name *</span><input type="text" name="first_name" value="<?= h($submitted['first_name']) ?>" autocomplete="given-name" required></label>
                            <label><span>Surname *</span><input type="text" name="surname" value="<?= h($submitted['surname']) ?>" autocomplete="family-name" required></label>
                            <label><span>Cellphone number *</span><input type="tel" name="phone" value="<?= h($submitted['phone']) ?>" autocomplete="tel" required></label>
                            <label><span>Email address <small>(optional)</small></span><input type="email" name="email" value="<?= h($submitted['email']) ?>" autocomplete="email"></label>
                        </div>
                        <label><span>What were you trying to purchase or apply for? *</span><select name="credit_type" required><option value="">Please select</option><?php foreach ($creditTypes as $value => $label): ?><option value="<?= h($value) ?>" <?= $submitted['credit_type'] === $value ? 'selected' : '' ?>><?= h($label) ?></option><?php endforeach; ?></select></label>
                        <label><span>What happened? *</span><select name="decline_reason" required><option value="">Please select</option><?php foreach ($declineReasons as $value => $label): ?><option value="<?= h($value) ?>" <?= $submitted['decline_reason'] === $value ? 'selected' : '' ?>><?= h($label) ?></option><?php endforeach; ?></select></label>
                        <label><span>Anything else you would like us to know? <small>(optional)</small></span><textarea name="message" rows="3" maxlength="1000" placeholder="Briefly tell us what happened."><?= h($submitted['message']) ?></textarea></label>
                        <label class="credit-consent"><input type="checkbox" name="consent" value="1" required><span>I agree that IY Finance Solutions may contact me about this enquiry and use the information provided to respond to my request.</span></label>
                        <button class="button button-accent credit-submit" type="submit"><i class="fa-solid fa-paper-plane" aria-hidden="true"></i> Submit my enquiry</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require PROJECT_ROOT . '/includes/footer.php'; ?>
