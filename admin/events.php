<?php
declare(strict_types=1);

require dirname(__DIR__) . '/config/bootstrap.php';
require PROJECT_ROOT . '/config/content.php';
require_admin();

$events = content_read_records(EVENTS_RECORD_FILE);
usort($events, static fn (array $left, array $right): int => strcmp((string) ($right['start_date'] ?? ''), (string) ($left['start_date'] ?? '')));
$categories = content_event_categories();
$statusLabels = ['upcoming' => 'Upcoming', 'today' => 'Today', 'completed' => 'Completed', 'cancelled' => 'Cancelled', 'postponed' => 'Postponed'];
$visibleCount = count(array_filter($events, static fn (array $item): bool => !empty($item['visible'])));
$upcomingCount = count(array_filter($events, static function (array $item): bool {
    $status = content_event_status($item);
    return in_array($status, ['upcoming', 'today'], true)
        || ($status === 'postponed' && (string) ($item['start_date'] ?? '') >= content_today());
}));
$adminTitle = 'Manage events';
$adminPageStyles = ['css/content-admin.css'];
require PROJECT_ROOT . '/includes/admin-header.php';
?>

<div class="admin-shell dashboard-header content-admin-header">
    <div><span class="admin-eyebrow">Events calendar</span><h1>Manage events and activities</h1><p>Publish future dates and preserve a searchable record of activities that have already taken place.</p></div>
    <div class="content-admin-actions"><a class="admin-button secondary" href="<?= h(site_url('events.php')) ?>" target="_blank" rel="noopener"><i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i> View calendar</a><a class="admin-button primary" href="<?= h(site_url('admin/event-form.php')) ?>"><i class="fa-solid fa-plus" aria-hidden="true"></i> New event</a></div>
</div>

<div class="admin-shell content-admin-summary">
    <div class="summary-tile"><span>Total events</span><strong><?= count($events) ?></strong></div>
    <div class="summary-tile"><span>Upcoming or active</span><strong><?= $upcomingCount ?></strong></div>
    <div class="summary-tile"><span>Visible events</span><strong><?= $visibleCount ?></strong></div>
</div>

<section class="admin-shell">
    <?php if ($events === []): ?>
        <div class="admin-empty"><i class="fa-regular fa-calendar-plus" aria-hidden="true"></i><h3>No events created yet</h3><p>Add the first workshop, activation, appearance or community activity.</p><a class="admin-button primary" href="<?= h(site_url('admin/event-form.php')) ?>">Create first event</a></div>
    <?php else: ?>
        <div class="admin-records">
            <?php foreach ($events as $event): $image = (string) ($event['image'] ?? ''); $status = content_event_status($event); ?>
                <article class="admin-record">
                    <div class="admin-record-media"><?php if ($image !== ''): ?><img src="<?= h(content_media_url('event', $image)) ?>" alt="" loading="lazy"><?php else: ?><span class="admin-record-placeholder"><i class="fa-regular fa-calendar" aria-hidden="true"></i></span><?php endif; ?></div>
                    <div class="admin-record-copy">
                        <div class="admin-record-meta"><span><?= h($categories[$event['category'] ?? ''] ?? 'Event') ?></span><span><?= h(content_event_date_label($event)) ?></span><span><?= h($statusLabels[$status] ?? ucfirst($status)) ?></span><span class="<?= !empty($event['visible']) ? 'is-visible' : 'is-hidden' ?>"><?= !empty($event['visible']) ? 'Visible' : 'Hidden' ?></span></div>
                        <h2><?= h((string) ($event['title'] ?? 'Untitled event')) ?></h2>
                        <p><?= h(trim((string) ($event['location'] ?? '') . (!empty($event['address']) ? ' · ' . (string) $event['address'] : ''))) ?></p>
                    </div>
                    <div class="admin-record-actions">
                        <a class="admin-button secondary small" href="<?= h(site_url('admin/event-form.php?id=' . rawurlencode((string) $event['id']))) ?>"><i class="fa-solid fa-pen" aria-hidden="true"></i> Edit</a>
                        <form action="<?= h(site_url('admin/event-delete.php')) ?>" method="post" data-delete-form data-delete-message="Delete this event and its uploaded image? This cannot be undone."><input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>"><input type="hidden" name="id" value="<?= h((string) $event['id']) ?>"><button class="admin-button danger small" type="submit"><i class="fa-solid fa-trash-can" aria-hidden="true"></i> Delete</button></form>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php require PROJECT_ROOT . '/includes/admin-footer.php'; ?>
