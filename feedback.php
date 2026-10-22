<?php
declare(strict_types=1);

require __DIR__ . '/config/bootstrap.php';
require PROJECT_ROOT . '/config/client-feedback.php';

$pageTitle = 'Client Feedback | IY Finance Solutions';
$pageDescription = 'Share your experience with IY Finance Solutions. Feedback is reviewed privately and testimonials are only published with permission.';
$activePage = '';
$bodyClass = 'client-feedback-page';
$pageStyles = ['css/client-feedback.css'];

start_admin_session();
$feedbackState = $_SESSION['client_feedback_state'] ?? [];
unset($_SESSION['client_feedback_state']);
$feedbackStatus = is_array($feedbackState) ? (string) ($feedbackState['status'] ?? '') : '';
$feedbackMessage = is_array($feedbackState) ? (string) ($feedbackState['message'] ?? '') : '';
$old = is_array($feedbackState['old'] ?? null) ? $feedbackState['old'] : [];

require PROJECT_ROOT . '/includes/header.php';
?>
<section class="feedback-hero">
    <div class="shell feedback-hero-inner">
        <nav class="breadcrumbs" aria-label="Breadcrumb">
            <a href="<?= h(site_url('index.php')) ?>">Home</a>
            <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
            <span>Client feedback</span>
        </nav>
        <span class="eyebrow light">Your experience matters</span>
        <h1>Tell us how we did.</h1>
        <p>We welcome honest feedback in any language. Tell us what went well, what we could improve, or how we supported you.</p>
        <a class="button button-light" href="<?= h(site_url('client-reviews.php')) ?>">Read client reviews <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
    </div>
</section>

<section class="section feedback-section">
    <div class="shell feedback-layout">
        <aside class="feedback-aside">
            <span class="feedback-icon"><i class="fa-solid fa-comments" aria-hidden="true"></i></span>
            <h2>Your voice helps us improve.</h2>
            <p>Whether your experience was positive or we could have done better, your feedback helps our team understand what matters to clients.</p>
            <ul>
                <li><i class="fa-solid fa-lock" aria-hidden="true"></i> Your feedback stays private while it is reviewed.</li>
                <li><i class="fa-solid fa-language" aria-hidden="true"></i> Write in the language you feel comfortable using.</li>
                <li><i class="fa-solid fa-user-secret" aria-hidden="true"></i> You may submit feedback anonymously.</li>
            </ul>
        </aside>

        <div class="feedback-form-card">
            <div class="feedback-form-heading">
                <span class="eyebrow">Client feedback</span>
                <h2>Share your experience</h2>
                <p>Fields marked optional can be left blank. A star rating is optional.</p>
            </div>

            <?php if ($feedbackMessage !== ''): ?>
                <div class="feedback-alert <?= $feedbackStatus === 'success' ? 'is-success' : 'is-error' ?>" role="status" aria-live="polite">
                    <i class="fa-solid <?= $feedbackStatus === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation' ?>" aria-hidden="true"></i>
                    <span><?= h($feedbackMessage) ?></span>
                </div>
            <?php endif; ?>

            <form action="<?= h(site_url('feedback-submit.php')) ?>" method="post" class="client-feedback-form">
                <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                <input type="hidden" name="form_started_at" value="<?= time() ?>">
                <div class="feedback-honeypot" aria-hidden="true">
                    <label for="feedback-company-site">Leave this field empty</label>
                    <input id="feedback-company-site" type="text" name="company_website" tabindex="-1" autocomplete="off">
                </div>

                <div class="feedback-field-grid">
                    <div class="feedback-field">
                        <label for="feedback-service">Which service did you use? <span class="optional-label">Optional</span></label>
                        <select id="feedback-service" name="service">
                            <option value="">Select a service (optional)</option>
                            <?php foreach (client_feedback_services() as $service): ?>
                                <option value="<?= h($service) ?>" <?= (string) ($old['service'] ?? '') === $service ? 'selected' : '' ?>><?= h($service) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="feedback-field">
                        <label for="feedback-language">Language <span class="optional-label">Optional</span></label>
                        <input id="feedback-language" name="language" type="text" maxlength="60" value="<?= h((string) ($old['language'] ?? '')) ?>" placeholder="e.g. isiZulu, English, Sesotho">
                    </div>
                    <div class="feedback-field">
                        <label for="feedback-name">Your name <span class="optional-label">Optional</span></label>
                        <input id="feedback-name" name="name" type="text" maxlength="100" value="<?= h((string) ($old['name'] ?? '')) ?>" autocomplete="name" placeholder="Leave blank to stay anonymous">
                    </div>
                    <div class="feedback-field">
                        <label for="feedback-email">Email address <span class="optional-label">Optional</span></label>
                        <input id="feedback-email" name="email" type="email" maxlength="254" value="<?= h((string) ($old['email'] ?? '')) ?>" autocomplete="email" placeholder="Only if you would like us to follow up">
                    </div>
                </div>

                <fieldset class="feedback-rating-field">
                    <legend>How would you rate your experience? <span class="optional-label">Optional</span></legend>
                    <div class="feedback-rating-options">
                        <?php for ($score = 1; $score <= 5; $score++): ?>
                            <label>
                                <input type="radio" name="rating" value="<?= $score ?>" <?= (string) ($old['rating'] ?? '') === (string) $score ? 'checked' : '' ?>>
                                <span><i class="fa-solid fa-star" aria-hidden="true"></i><span class="sr-only"><?= $score ?> out of 5</span></span>
                            </label>
                        <?php endfor; ?>
                        <span class="feedback-rating-hint">Choose 1–5 stars, or skip this question.</span>
                    </div>
                </fieldset>

                <div class="feedback-field">
                    <label for="feedback-message">Your feedback <span aria-hidden="true">*</span></label>
                    <textarea id="feedback-message" name="feedback" rows="7" minlength="10" maxlength="3000" required placeholder="Share your experience in your own words and language."><?= h((string) ($old['feedback'] ?? '')) ?></textarea>
                    <small>10–3,000 characters. Please do not include ID numbers, account numbers, passwords or detailed financial information.</small>
                </div>

                <div class="feedback-consent-box">
                    <label class="feedback-checkbox">
                        <input type="checkbox" name="privacy_acknowledged" value="1" required <?= !empty($old['privacy_acknowledged']) ? 'checked' : '' ?>>
                        <span>I have read the <a href="<?= h(site_url('privacy.php')) ?>">privacy information</a> and understand that IY Finance Solutions will use this feedback to review and improve its services. <strong>Required</strong></span>
                    </label>
                    <label class="feedback-checkbox">
                        <input type="checkbox" name="publication_consent" value="1" <?= !empty($old['publication_consent']) ? 'checked' : '' ?>>
                        <span>I give permission for my feedback to be considered for publication as a testimonial after review. I understand that it will not be published unless approved. <strong>Optional</strong></span>
                    </label>
                    <label class="feedback-checkbox feedback-name-consent">
                        <input type="checkbox" name="display_name_consent" value="1" <?= !empty($old['display_name_consent']) ? 'checked' : '' ?>>
                        <span>If I provided my name, I also give permission for my name to appear with the testimonial. Leave unchecked to publish anonymously. <strong>Optional</strong></span>
                    </label>
                </div>

                <div class="feedback-form-footer">
                    <p><i class="fa-solid fa-shield-halved" aria-hidden="true"></i> Feedback is private by default. Submitting it does not guarantee publication or a particular service outcome.</p>
                    <button class="button button-primary" type="submit"><span>Submit feedback</span><i class="fa-solid fa-paper-plane" aria-hidden="true"></i></button>
                </div>
            </form>
        </div>
    </div>
</section>
<?php require PROJECT_ROOT . '/includes/footer.php'; ?>
