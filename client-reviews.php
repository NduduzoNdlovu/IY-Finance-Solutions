<?php
declare(strict_types=1);

require __DIR__ . '/config/bootstrap.php';
require PROJECT_ROOT . '/config/client-feedback.php';

$pageTitle = 'Client Reviews | IY Finance Solutions';
$pageDescription = 'Read client experiences shared with permission by people who have used IY Finance Solutions.';
$activePage = 'reviews';
$bodyClass = 'client-reviews-page';
$pageStyles = ['css/client-feedback.css'];

$reviews = array_values(array_filter(client_feedback_read(), static function (array $record): bool {
    return ($record['status'] ?? '') === 'approved'
        && ($record['publication_consent'] ?? false) === true
        && trim((string) ($record['feedback'] ?? '')) !== '';
}));
usort($reviews, static fn (array $left, array $right): int => strcmp((string) ($right['reviewed_at'] ?? $right['created_at'] ?? ''), (string) ($left['reviewed_at'] ?? $left['created_at'] ?? '')));

require PROJECT_ROOT . '/includes/header.php';
?>
<section class="feedback-hero reviews-hero">
    <div class="shell feedback-hero-inner">
        <nav class="breadcrumbs" aria-label="Breadcrumb">
            <a href="<?= h(site_url('index.php')) ?>">Home</a>
            <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
            <span>Client reviews</span>
        </nav>
        <span class="eyebrow light">Shared with permission</span>
        <h1>Experiences from our clients.</h1>
        <p>Every story shown here has been reviewed and approved for publication. Individual experiences and outcomes may differ.</p>
        <a class="button button-light" href="<?= h(site_url('feedback.php')) ?>">Share your feedback <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
    </div>
</section>
<section class="section reviews-section">
    <div class="shell">
        <?php if ($reviews === []): ?>
            <div class="reviews-empty">
                <span class="feedback-icon"><i class="fa-regular fa-comments" aria-hidden="true"></i></span>
                <h2>Client stories will appear here.</h2>
                <p>We are building this collection from genuine feedback shared with permission. No reviews are published until they have been reviewed and approved.</p>
                <a class="button button-primary" href="<?= h(site_url('feedback.php')) ?>">Share your experience</a>
            </div>
        <?php else: ?>
            <div class="reviews-grid">
                <?php foreach ($reviews as $review): ?>
                    <article class="review-card">
                        <div class="review-card-mark" aria-hidden="true"><i class="fa-solid fa-quote-left"></i></div>
                        <?php if (isset($review['rating']) && is_int($review['rating']) && $review['rating'] >= 1 && $review['rating'] <= 5): ?>
                            <div class="review-stars" aria-label="Rated <?= (int) $review['rating'] ?> out of 5 stars">
                                <?php for ($star = 1; $star <= 5; $star++): ?><i class="fa-solid fa-star<?= $star <= $review['rating'] ? ' is-filled' : '' ?>" aria-hidden="true"></i><?php endfor; ?>
                            </div>
                        <?php endif; ?>
                        <blockquote><?= nl2br(h((string) $review['feedback'])) ?></blockquote>
                        <div class="review-card-footer">
                            <strong><?= !empty($review['display_name_consent']) && trim((string) ($review['name'] ?? '')) !== '' ? h((string) $review['name']) : 'Anonymous client' ?></strong>
                            <?php if (trim((string) ($review['service'] ?? '')) !== ''): ?><span><?= h((string) $review['service']) ?></span><?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <p class="reviews-disclaimer">Testimonials reflect individual experiences and are not a guarantee of a particular credit, financial or service outcome.</p>
    </div>
</section>
<?php require PROJECT_ROOT . '/includes/footer.php'; ?>
