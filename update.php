<?php
declare(strict_types=1);

require __DIR__ . '/config/bootstrap.php';
require PROJECT_ROOT . '/config/content.php';

$slug = trim((string) ($_GET['slug'] ?? ''));
$update = content_find_update_by_slug(content_public_updates(true), $slug);
if ($update === null) {
    http_response_code(404);
    $pageTitle = 'Update not found | IY Finance Solutions';
    $pageDescription = 'The requested IY Finance Solutions update could not be found.';
    $activePage = 'updates';
    $bodyClass = 'updates-page';
    $pageStyles = ['css/updates-events.css'];
    require PROJECT_ROOT . '/includes/header.php';
    ?>
    <section class="section"><div class="shell"><div class="content-empty"><i class="fa-regular fa-circle-question" aria-hidden="true"></i><h1>Update not found</h1><p>This post may have been removed or the address may be incorrect.</p><a class="button button-primary" href="<?= h(site_url('updates.php')) ?>">Return to news & updates</a></div></div></section>
    <?php
    require PROJECT_ROOT . '/includes/footer.php';
    exit;
}

$categories = content_update_categories();
$image = (string) ($update['image'] ?? '');
$detailUrl = content_absolute_url('update.php?slug=' . rawurlencode((string) $update['slug']));
$pageTitle = (string) $update['title'] . ' | IY Finance Solutions';
$pageDescription = content_excerpt((string) ($update['description'] ?? ''), 155);
$activePage = 'updates';
$bodyClass = 'updates-page update-detail-page';
$pageStyles = ['css/updates-events.css'];
require PROJECT_ROOT . '/includes/header.php';
?>

<section class="content-detail-hero">
    <div class="shell">
        <nav class="breadcrumbs" aria-label="Breadcrumb"><a href="<?= h(site_url('index.php')) ?>">Home</a><i class="fa-solid fa-chevron-right" aria-hidden="true"></i><a href="<?= h(site_url('updates.php')) ?>">Updates</a><i class="fa-solid fa-chevron-right" aria-hidden="true"></i><span><?= h((string) $update['title']) ?></span></nav>
        <div class="content-meta light-meta"><span><?= h($categories[$update['category'] ?? ''] ?? 'Update') ?></span><time datetime="<?= h((string) $update['published_on']) ?>"><?= h(content_human_date((string) $update['published_on'])) ?></time><?php if (content_update_is_expired($update)): ?><span>Archived</span><?php endif; ?></div>
        <h1><?= h((string) $update['title']) ?></h1>
    </div>
</section>

<article class="section update-detail">
    <div class="shell update-detail-grid">
        <?php if ($image !== ''): ?>
            <figure class="flyer-panel reveal">
                <img src="<?= h(content_media_url('update', $image)) ?>" alt="<?= h((string) $update['title']) ?>">
                <figcaption><a href="<?= h(content_media_url('update', $image)) ?>" download><i class="fa-solid fa-download" aria-hidden="true"></i> Download flyer image</a></figcaption>
            </figure>
        <?php endif; ?>
        <div class="update-detail-copy reveal">
            <span class="eyebrow">IY Finance update</span>
            <div class="prose"><?= nl2br(h((string) ($update['description'] ?? ''))) ?></div>
            <?php if (!empty($update['expires_on'])): ?><p class="expiry-note"><i class="fa-regular fa-clock" aria-hidden="true"></i> Valid until <?= h(content_human_date((string) $update['expires_on'])) ?></p><?php endif; ?>
            <div class="detail-actions">
                <?php if (!empty($update['cta_url'])): ?><a class="button button-primary" href="<?= h((string) $update['cta_url']) ?>"<?= str_starts_with((string) $update['cta_url'], 'http') ? ' target="_blank" rel="noopener"' : '' ?>><?= h((string) ($update['cta_label'] ?: 'Learn more')) ?> <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a><?php endif; ?>
                <a class="button button-whatsapp" href="<?= h(content_share_url($detailUrl, (string) $update['title'])) ?>" target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> Share on WhatsApp</a>
            </div>
            <a class="back-link" href="<?= h(site_url('updates.php')) ?>"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Back to all updates</a>
        </div>
    </div>
</article>

<?php require PROJECT_ROOT . '/includes/footer.php'; ?>
