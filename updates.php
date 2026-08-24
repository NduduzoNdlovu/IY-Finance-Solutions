<?php
declare(strict_types=1);

require __DIR__ . '/config/bootstrap.php';
require PROJECT_ROOT . '/config/content.php';

$allUpdates = content_public_updates(true);
$currentUpdates = array_values(array_filter($allUpdates, static fn (array $update): bool => !content_update_is_expired($update)));
$archivedUpdates = array_values(array_filter($allUpdates, 'content_update_is_expired'));
$categories = content_update_categories();
$pageTitle = 'News & Updates | IY Finance Solutions';
$pageDescription = 'Read the latest IY Finance Solutions announcements, flyers, branch news, offers and customer notices.';
$activePage = 'updates';
$bodyClass = 'updates-page';
$pageStyles = ['css/updates-events.css'];
$pageScript = 'updates.js';
require PROJECT_ROOT . '/includes/header.php';

$renderUpdateCard = static function (array $update) use ($categories, $site): void {
    $image = (string) ($update['image'] ?? '');
    $detailUrl = content_absolute_url('update.php?slug=' . rawurlencode((string) $update['slug']));
    $shareUrl = content_share_url($detailUrl, (string) $update['title']);
    ?>
    <article class="update-card reveal" data-update-card data-category="<?= h((string) ($update['category'] ?? 'announcement')) ?>">
        <?php if ($image !== ''): ?>
            <a class="update-card-media" href="<?= h($detailUrl) ?>" aria-label="Read <?= h((string) $update['title']) ?>">
                <img src="<?= h(content_media_url('update', $image)) ?>" alt="<?= h((string) $update['title']) ?>" loading="lazy">
                <?php if (!empty($update['featured'])): ?><span class="featured-flag"><i class="fa-solid fa-star" aria-hidden="true"></i> Featured</span><?php endif; ?>
            </a>
        <?php else: ?>
            <a class="update-card-media update-card-placeholder" href="<?= h($detailUrl) ?>" aria-label="Read <?= h((string) $update['title']) ?>"><i class="fa-solid fa-bullhorn" aria-hidden="true"></i></a>
        <?php endif; ?>
        <div class="update-card-body">
            <div class="content-meta">
                <span><?= h($categories[$update['category'] ?? ''] ?? 'Update') ?></span>
                <time datetime="<?= h((string) $update['published_on']) ?>"><?= h(content_human_date((string) $update['published_on'])) ?></time>
            </div>
            <h2><a href="<?= h($detailUrl) ?>"><?= h((string) $update['title']) ?></a></h2>
            <p><?= h(content_excerpt((string) ($update['description'] ?? ''), 165)) ?></p>
            <div class="card-actions">
                <a class="text-link" href="<?= h($detailUrl) ?>">Read more <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                <a class="icon-link" href="<?= h($shareUrl) ?>" target="_blank" rel="noopener" aria-label="Share <?= h((string) $update['title']) ?> on WhatsApp"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i></a>
            </div>
        </div>
    </article>
    <?php
};
?>

<section class="content-hero updates-hero">
    <div class="shell content-hero-inner">
        <nav class="breadcrumbs" aria-label="Breadcrumb"><a href="<?= h(site_url('index.php')) ?>">Home</a><i class="fa-solid fa-chevron-right" aria-hidden="true"></i><span>News & updates</span></nav>
        <span class="eyebrow light">Stay informed</span>
        <h1>News, announcements and opportunities</h1>
        <p>Important IY Finance Solutions updates, community activity, branch notices and current promotional flyers - all in one trusted place.</p>
        <div class="content-hero-actions">
            <a class="button button-accent" href="#latest-updates">View latest updates <i class="fa-solid fa-arrow-down" aria-hidden="true"></i></a>
            <a class="button button-ghost" href="<?= h(site_url('events.php')) ?>"><i class="fa-regular fa-calendar" aria-hidden="true"></i> Events calendar</a>
        </div>
    </div>
</section>

<section class="section content-list-section" id="latest-updates">
    <div class="shell">
        <div class="section-heading split-heading reveal">
            <div><span class="eyebrow">Latest from IY</span><h2>Current news and notices</h2></div>
            <p>Filter the updates below or open a post to view its full information and downloadable flyer.</p>
        </div>

        <?php if ($currentUpdates !== []): ?>
            <div class="content-filter-bar reveal" aria-label="Filter updates">
                <button class="is-active" type="button" data-update-filter="all" aria-pressed="true">All updates</button>
                <?php foreach ($categories as $key => $label): ?>
                    <?php if (array_filter($currentUpdates, static fn (array $item): bool => ($item['category'] ?? '') === $key)): ?>
                        <button type="button" data-update-filter="<?= h($key) ?>" aria-pressed="false"><?= h($label) ?></button>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <p class="filter-result" data-filter-result aria-live="polite"></p>
            <div class="updates-grid" data-updates-grid>
                <?php foreach ($currentUpdates as $update): $renderUpdateCard($update); endforeach; ?>
            </div>
        <?php else: ?>
            <div class="content-empty reveal">
                <i class="fa-regular fa-newspaper" aria-hidden="true"></i>
                <h2>New updates are on the way</h2>
                <p>Please check back soon or contact our team for current information.</p>
                <a class="button button-primary" href="<?= h(site_url('contact.php')) ?>">Contact IY Finance</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php if ($archivedUpdates !== []): ?>
<section class="section update-archive-section">
    <div class="shell">
        <details class="archive-panel reveal">
            <summary><span><i class="fa-solid fa-box-archive" aria-hidden="true"></i><strong>Past announcements</strong><small><?= count($archivedUpdates) ?> archived <?= count($archivedUpdates) === 1 ? 'update' : 'updates' ?></small></span><i class="fa-solid fa-chevron-down" aria-hidden="true"></i></summary>
            <div class="updates-grid archive-grid">
                <?php foreach ($archivedUpdates as $update): $renderUpdateCard($update); endforeach; ?>
            </div>
        </details>
    </div>
</section>
<?php endif; ?>

<section class="content-crosslink">
    <div class="shell content-crosslink-inner reveal">
        <div><span class="eyebrow light">Plan ahead</span><h2>See what is happening next</h2><p>View upcoming branch activations, workshops and community events on the IY calendar.</p></div>
        <a class="button button-light" href="<?= h(site_url('events.php')) ?>">Open events calendar <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
    </div>
</section>

<?php require PROJECT_ROOT . '/includes/footer.php'; ?>
