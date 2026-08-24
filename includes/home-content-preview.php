<?php
declare(strict_types=1);

$homeUpdates = array_slice(content_public_updates(false), 0, 3);
$homeEvents = array_values(array_filter(content_public_events(), static function (array $event): bool {
    $status = content_event_status($event);
    return in_array($status, ['upcoming', 'today'], true)
        || ($status === 'postponed' && (string) ($event['start_date'] ?? '') >= content_today());
}));
$homeEvents = array_slice($homeEvents, 0, 3);
$homeUpdateCategories = content_update_categories();
?>
<?php if ($homeUpdates !== [] || $homeEvents !== []): ?>
<section class="section home-content-preview">
    <div class="shell">
        <div class="section-heading split-heading reveal">
            <div><span class="eyebrow">What’s happening</span><h2>Latest from IY Finance</h2></div>
            <p>Stay informed about company announcements, helpful notices and upcoming IY events.</p>
        </div>

        <div class="home-content-columns">
            <?php if ($homeUpdates !== []): ?>
            <section class="home-preview-column reveal">
                <div class="home-preview-heading"><h3><i class="fa-solid fa-bullhorn" aria-hidden="true"></i> News & updates</h3><a href="<?= h(site_url('updates.php')) ?>">View all <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a></div>
                <div class="home-preview-list">
                    <?php foreach ($homeUpdates as $update): ?>
                        <a class="home-update-row" href="<?= h(site_url('update.php?slug=' . rawurlencode((string) $update['slug']))) ?>">
                            <?php if (!empty($update['image'])): ?><img src="<?= h(content_media_url('update', (string) $update['image'])) ?>" alt="" loading="lazy"><?php else: ?><span class="home-preview-placeholder"><i class="fa-solid fa-bullhorn" aria-hidden="true"></i></span><?php endif; ?>
                            <span><small><?= h($homeUpdateCategories[$update['category'] ?? ''] ?? 'Update') ?> · <?= h(content_human_date((string) $update['published_on'])) ?></small><strong><?= h((string) $update['title']) ?></strong></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>

            <?php if ($homeEvents !== []): ?>
            <section class="home-preview-column reveal">
                <div class="home-preview-heading"><h3><i class="fa-regular fa-calendar" aria-hidden="true"></i> Upcoming events</h3><a href="<?= h(site_url('events.php')) ?>">Full calendar <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a></div>
                <div class="home-preview-list">
                    <?php foreach ($homeEvents as $event): ?>
                        <a class="home-event-row" href="<?= h(site_url('events.php#event-' . rawurlencode((string) $event['id']))) ?>">
                            <span class="home-date-tile"><strong><?= h((new DateTimeImmutable((string) $event['start_date']))->format('d')) ?></strong><small><?= h((new DateTimeImmutable((string) $event['start_date']))->format('M')) ?></small></span>
                            <span><small><?= h((string) ($event['location'] ?: 'IY Finance Solutions')) ?></small><strong><?= h((string) $event['title']) ?></strong><em><?= h(content_human_time((string) ($event['start_time'] ?? ''))) ?></em></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>
