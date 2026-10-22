<?php
declare(strict_types=1);

require __DIR__ . '/config/bootstrap.php';
$pageTitle = 'Feedback Privacy Information | IY Finance Solutions';
$pageDescription = 'Learn how IY Finance Solutions uses client feedback and how to ask about your personal information.';
$activePage = '';
$bodyClass = 'client-reviews-page';
$pageStyles = ['css/client-feedback.css'];
require PROJECT_ROOT . '/includes/header.php';
?>
<section class="feedback-hero">
    <div class="shell feedback-hero-inner">
        <nav class="breadcrumbs" aria-label="Breadcrumb"><a href="<?= h(site_url('index.php')) ?>">Home</a><i class="fa-solid fa-chevron-right" aria-hidden="true"></i><a href="<?= h(site_url('feedback.php')) ?>">Client feedback</a><i class="fa-solid fa-chevron-right" aria-hidden="true"></i><span>Privacy information</span></nav>
        <span class="eyebrow light">Your information</span>
        <h1>Feedback privacy information</h1>
        <p>We use feedback to understand client experiences, improve our services and, only with your separate permission, consider publishing a testimonial.</p>
    </div>
</section>
<section class="section reviews-section">
    <div class="shell">
        <article class="review-card privacy-notice-card">
            <h2>How feedback is handled</h2>
            <p>Feedback is stored privately for review by authorised IY Finance Solutions administrators. Providing feedback does not automatically make it public.</p>
            <h3>What we collect</h3>
            <p>Depending on what you choose to provide, a submission may include your feedback, the service you used, language, optional name, optional email address and an optional rating. Please do not include your identity number, account numbers, passwords or unnecessary financial details.</p>
            <h3>How we use it</h3>
            <p>We use feedback to review and improve our services. If you separately consent to publication, an administrator may consider your feedback as a testimonial. Publication occurs only after review and approval. Your name is displayed only if you have separately agreed to the use of your name; otherwise a published testimonial is shown anonymously.</p>
            <h3>Keeping information private</h3>
            <p>Contact details and feedback awaiting review are not displayed on the public reviews page. We take reasonable technical and organisational steps to protect records and restrict access. No website can guarantee absolute security.</p>
            <h3>Your choices and questions</h3>
            <p>You may leave your name and rating blank. You can submit feedback without agreeing to publication. To ask about feedback you submitted, request correction or ask that it be removed, contact <a href="mailto:<?= h($site['email']) ?>"><?= h($site['email']) ?></a> and provide enough information for the team to identify the submission without sending sensitive financial information.</p>
            <p>This notice summarises how the feedback feature is intended to work and is not a substitute for the company’s full privacy policy or legal advice. IY Finance Solutions should review it against its actual retention practices and applicable POPIA obligations before launch.</p>
            <a class="button button-primary" href="<?= h(site_url('feedback.php')) ?>">Return to feedback</a>
        </article>
    </div>
</section>
<?php require PROJECT_ROOT . '/includes/footer.php'; ?>
