<?php
declare(strict_types=1);

require dirname(__DIR__) . '/config/bootstrap.php';
require PROJECT_ROOT . '/config/content.php';
require_admin();

$updates = content_read_records(UPDATES_RECORD_FILE);
usort($updates, static fn (array $left, array $right): int => strcmp((string) ($right['published_on'] ?? ''), (string) ($left['published_on'] ?? '')));
$categories = content_update_categories();
$visibleCount = count(array_filter($updates, static fn (array $item): bool => !empty($item['visible'])));
$featuredCount = count(array_filter($updates, static fn (array $item): bool => !empty($item['featured'])));
$adminTitle = 'Manage news and updates';
$adminPageStyles = ['css/content-admin.css'];
require PROJECT_ROOT . '/includes/admin-header.php';
?>

<div class="admin-shell dashboard-header content-admin-header">
    <div><span class="admin-eyebrow">News & updates</span><h1>Manage announcements and flyers</h1><p>Create, edit or remove the posts shown on the public News & Updates page.</p></div>
    <div class="content-admin-actions"><a class="admin-button secondary" href="<?= h(site_url('updates.php')) ?>" target="_blank" rel="noopener"><i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i> View public page</a><a class="admin-button primary" href="<?= h(site_url('admin/update-form.php')) ?>"><i class="fa-solid fa-plus" aria-hidden="true"></i> New update</a></div>
</div>

<div class="admin-shell content-admin-summary">
    <div class="summary-tile"><span>Total posts</span><strong><?= count($updates) ?></strong></div>
    <div class="summary-tile"><span>Visible posts</span><strong><?= $visibleCount ?></strong></div>
    <div class="summary-tile"><span>Featured posts</span><strong><?= $featuredCount ?></strong></div>
</div>

<section class="admin-shell">
    <?php if ($updates === []): ?>
        <div class="admin-empty"><i class="fa-solid fa-bullhorn" aria-hidden="true"></i><h3>No updates created yet</h3><p>Create the first announcement, customer notice or promotional flyer.</p><a class="admin-button primary" href="<?= h(site_url('admin/update-form.php')) ?>">Create first update</a></div>
    <?php else: ?>
        <div class="admin-records">
            <?php foreach ($updates as $update): $image = (string) ($update['image'] ?? ''); ?>
                <article class="admin-record">
                    <div class="admin-record-media"><?php if ($image !== ''): ?><img src="<?= h(content_media_url('update', $image)) ?>" alt="" loading="lazy"><?php else: ?><span class="admin-record-placeholder"><i class="fa-solid fa-bullhorn" aria-hidden="true"></i></span><?php endif; ?></div>
                    <div class="admin-record-copy">
                        <div class="admin-record-meta"><span><?= h($categories[$update['category'] ?? ''] ?? 'Update') ?></span><span><?= h(content_human_date((string) ($update['published_on'] ?? ''))) ?></span><span class="<?= !empty($update['visible']) ? 'is-visible' : 'is-hidden' ?>"><?= !empty($update['visible']) ? 'Visible' : 'Hidden' ?></span><?php if (!empty($update['featured'])): ?><span>Featured</span><?php endif; ?><?php if (content_update_is_expired($update)): ?><span>Expired</span><?php endif; ?></div>
                        <h2><?= h((string) ($update['title'] ?? 'Untitled update')) ?></h2>
                        <p><?= h(content_excerpt((string) ($update['description'] ?? ''), 150)) ?></p>
                    </div>
                    <div class="admin-record-actions">
                        <a class="admin-button secondary small" href="<?= h(site_url('admin/update-form.php?id=' . rawurlencode((string) $update['id']))) ?>"><i class="fa-solid fa-pen" aria-hidden="true"></i> Edit</a>
                        <form action="<?= h(site_url('admin/update-delete.php')) ?>" method="post" data-delete-form data-delete-message="Delete this update and its uploaded image? This cannot be undone."><input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>"><input type="hidden" name="id" value="<?= h((string) $update['id']) ?>"><button class="admin-button danger small" type="submit"><i class="fa-solid fa-trash-can" aria-hidden="true"></i> Delete</button></form>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php require PROJECT_ROOT . '/includes/admin-footer.php'; ?>
