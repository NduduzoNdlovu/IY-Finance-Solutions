<?php
declare(strict_types=1);

require __DIR__ . '/config/bootstrap.php';
$images = gallery_images();
$pageTitle = 'Gallery | IY Finance Solutions';
$pageDescription = 'Explore company, team and community moments from IY Finance Solutions.';
$activePage = 'gallery';
$bodyClass = 'gallery-page';
$pageScript = 'gallery.js';
require PROJECT_ROOT . '/includes/header.php';
?>

<section class="page-hero gallery-hero">
    <div class="shell page-hero-inner">
        <nav class="breadcrumbs" aria-label="Breadcrumb"><a href="<?= h(site_url('index.php')) ?>">Home</a><i class="fa-solid fa-chevron-right" aria-hidden="true"></i><span>Gallery</span></nav>
        <span class="eyebrow light">Our gallery</span>
        <h1>People, progress and moments that matter</h1>
        <p>A glimpse into the work, events and community behind IY Finance Solutions.</p>
    </div>
</section>

<section class="section gallery-section">
    <div class="shell">
        <div class="section-heading split-heading reveal">
            <div><span class="eyebrow">In pictures</span><h2>Inside IY Finance Solutions</h2></div>
            <p><?= count($images) ?> <?= count($images) === 1 ? 'moment' : 'moments' ?> from our gallery. Select any photograph to view it in full.</p>
        </div>

        <?php if ($images === []): ?>
            <div class="empty-state"><i class="fa-regular fa-images" aria-hidden="true"></i><h2>Gallery updates are coming soon</h2><p>Please check back for new company moments and events.</p></div>
        <?php else: ?>
            <div class="gallery-grid" data-gallery>
                <?php foreach ($images as $index => $image): ?>
                    <button class="gallery-card reveal" type="button" data-gallery-item data-index="<?= $index ?>" data-src="<?= h($image['url']) ?>" data-alt="IY Finance Solutions gallery photograph <?= $index + 1 ?>">
                        <img src="<?= h($image['url']) ?>" alt="IY Finance Solutions gallery photograph <?= $index + 1 ?>" width="<?= $image['width'] ?>" height="<?= $image['height'] ?>" loading="lazy">
                        <span><i class="fa-solid fa-expand" aria-hidden="true"></i><span class="sr-only">View photograph <?= $index + 1 ?></span></span>
                    </button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<dialog class="gallery-lightbox" data-lightbox aria-label="Gallery image viewer">
    <button class="lightbox-close" type="button" data-lightbox-close aria-label="Close image viewer"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
    <button class="lightbox-nav previous" type="button" data-lightbox-previous aria-label="Previous image"><i class="fa-solid fa-chevron-left" aria-hidden="true"></i></button>
    <figure><img src="" alt="" data-lightbox-image><figcaption data-lightbox-caption></figcaption></figure>
    <button class="lightbox-nav next" type="button" data-lightbox-next aria-label="Next image"><i class="fa-solid fa-chevron-right" aria-hidden="true"></i></button>
</dialog>

<?php require PROJECT_ROOT . '/includes/footer.php'; ?>

