<?php
declare(strict_types=1);

require __DIR__ . '/config/bootstrap.php';
require PROJECT_ROOT . '/config/content.php';

$events = content_public_events();
$categories = content_event_categories();
$statusLabels = ['upcoming' => 'Upcoming', 'today' => 'Happening today', 'completed' => 'Completed', 'cancelled' => 'Cancelled', 'postponed' => 'Postponed'];
$upcomingEvents = array_values(array_filter($events, static function (array $event): bool {
    $status = content_event_status($event);
    return in_array($status, ['upcoming', 'today'], true)
        || (in_array($status, ['postponed', 'cancelled'], true) && (string) ($event['start_date'] ?? '') >= content_today());
}));
$pastEvents = array_values(array_filter($events, static function (array $event): bool {
    $status = content_event_status($event);
    return $status === 'completed'
        || (in_array($status, ['postponed', 'cancelled'], true) && (string) ($event['start_date'] ?? '') < content_today());
}));
usort($upcomingEvents, static fn (array $left, array $right): int => strcmp((string) $left['start_date'], (string) $right['start_date']));
usort($pastEvents, static fn (array $left, array $right): int => strcmp((string) $right['start_date'], (string) $left['start_date']));

$calendarPayload = array_map(static function (array $event) use ($categories, $statusLabels): array {
    $status = content_event_status($event);
    return [
        'id' => (string) $event['id'],
        'title' => (string) $event['title'],
        'category' => (string) ($event['category'] ?? ''),
        'categoryLabel' => $categories[$event['category'] ?? ''] ?? 'Event',
        'startDate' => (string) $event['start_date'],
        'endDate' => (string) ($event['end_date'] ?? ''),
        'time' => content_human_time((string) ($event['start_time'] ?? '')),
        'location' => (string) ($event['location'] ?? ''),
        'status' => $status,
        'statusLabel' => $statusLabels[$status] ?? ucfirst($status),
        'anchor' => '#event-' . (string) $event['id'],
    ];
}, $events);

$pageTitle = 'Events Calendar | IY Finance Solutions';
$pageDescription = 'View upcoming and past IY Finance Solutions workshops, activations, outreach programmes and community events.';
$activePage = 'events';
$bodyClass = 'events-page';
$pageStyles = ['css/updates-events.css'];
$pageScript = 'events-calendar.js';
require PROJECT_ROOT . '/includes/header.php';

$renderEventCard = static function (array $event) use ($categories, $statusLabels): void {
    $status = content_event_status($event);
    $image = (string) ($event['image'] ?? '');
    $eventUrl = content_absolute_url('events.php#event-' . rawurlencode((string) $event['id']));
    ?>
    <article class="event-card reveal" id="event-<?= h((string) $event['id']) ?>" data-event-card data-category="<?= h((string) ($event['category'] ?? 'community')) ?>" data-status="<?= h($status) ?>">
        <?php if ($image !== ''): ?><div class="event-card-media"><img src="<?= h(content_media_url('event', $image)) ?>" alt="<?= h((string) $event['title']) ?>" loading="lazy"></div><?php endif; ?>
        <div class="event-date-tile"><strong><?= h((new DateTimeImmutable((string) $event['start_date']))->format('d')) ?></strong><span><?= h((new DateTimeImmutable((string) $event['start_date']))->format('M')) ?></span></div>
        <div class="event-card-body">
            <div class="content-meta"><span><?= h($categories[$event['category'] ?? ''] ?? 'Event') ?></span><span class="status-badge status-<?= h($status) ?>"><?= h($statusLabels[$status] ?? ucfirst($status)) ?></span></div>
            <h3><?= h((string) $event['title']) ?></h3>
            <p><?= h(content_excerpt((string) ($event['description'] ?? ''), 190)) ?></p>
            <ul class="event-facts">
                <li><i class="fa-regular fa-calendar" aria-hidden="true"></i><span><?= h(content_event_date_label($event)) ?></span></li>
                <?php if (!empty($event['location'])): ?><li><i class="fa-solid fa-location-dot" aria-hidden="true"></i><span><strong><?= h((string) $event['location']) ?></strong><?php if (!empty($event['address'])): ?><small><?= h((string) $event['address']) ?></small><?php endif; ?></span></li><?php endif; ?>
            </ul>
            <div class="event-actions">
                <?php if (!empty($event['address']) || !empty($event['location'])): ?><a href="<?= h(content_directions_url($event)) ?>" target="_blank" rel="noopener"><i class="fa-solid fa-route" aria-hidden="true"></i> Directions</a><?php endif; ?>
                <a href="<?= h(site_url('event-ics.php?id=' . rawurlencode((string) $event['id']))) ?>"><i class="fa-regular fa-calendar-plus" aria-hidden="true"></i> Add to calendar</a>
                <?php if ($image !== ''): ?><a href="<?= h(content_media_url('event', $image)) ?>" download><i class="fa-solid fa-download" aria-hidden="true"></i> Flyer</a><?php endif; ?>
                <a href="<?= h(content_share_url($eventUrl, (string) $event['title'])) ?>" target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> Share</a>
                <?php if ($status === 'completed' && !empty($event['gallery_url'])): ?><a href="<?= h((string) $event['gallery_url']) ?>"><i class="fa-regular fa-images" aria-hidden="true"></i> View photos</a><?php endif; ?>
            </div>
        </div>
    </article>
    <?php
};
?>

<section class="content-hero events-hero">
    <div class="shell content-hero-inner">
        <nav class="breadcrumbs" aria-label="Breadcrumb"><a href="<?= h(site_url('index.php')) ?>">Home</a><i class="fa-solid fa-chevron-right" aria-hidden="true"></i><span>Events calendar</span></nav>
        <span class="eyebrow light">IY in action</span>
        <h1>Events, activations and community moments</h1>
        <p>Plan for what is coming next and keep a record of IY Finance Solutions events that have already taken place.</p>
        <div class="content-hero-actions"><a class="button button-accent" href="#events-calendar">Open calendar <i class="fa-solid fa-arrow-down" aria-hidden="true"></i></a><a class="button button-ghost" href="<?= h(site_url('updates.php')) ?>"><i class="fa-regular fa-newspaper" aria-hidden="true"></i> News & updates</a></div>
    </div>
</section>

<section class="section calendar-section" id="events-calendar">
    <div class="shell">
        <div class="section-heading split-heading reveal"><div><span class="eyebrow">Calendar</span><h2>Find an IY event</h2></div><p>Choose a highlighted date to see its events. On smaller screens, use the agenda below for a clear mobile-friendly view.</p></div>
        <div class="calendar-layout reveal" data-calendar-app>
            <section class="calendar-panel" aria-label="Events calendar">
                <div class="calendar-toolbar">
                    <button type="button" data-calendar-previous aria-label="Previous month"><i class="fa-solid fa-chevron-left" aria-hidden="true"></i></button>
                    <div><strong data-calendar-title>Events calendar</strong><button type="button" data-calendar-today>Today</button></div>
                    <button type="button" data-calendar-next aria-label="Next month"><i class="fa-solid fa-chevron-right" aria-hidden="true"></i></button>
                </div>
                <div class="calendar-weekdays" aria-hidden="true"><span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span><span>Sun</span></div>
                <div class="calendar-grid" data-calendar-grid></div>
                <div class="calendar-mobile-message"><i class="fa-regular fa-calendar-check" aria-hidden="true"></i><span>The full calendar becomes an easy-to-read agenda on mobile.</span></div>
            </section>
            <aside class="calendar-day-panel" aria-live="polite">
                <span class="eyebrow">Selected day</span>
                <h3 data-selected-date>Select a highlighted date</h3>
                <div data-selected-events><p class="calendar-help">Dates with an IY event are marked in green.</p></div>
            </aside>
        </div>
        <script id="events-calendar-data" type="application/json"><?= json_encode($calendarPayload, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES) ?></script>
    </div>
</section>

<section class="section events-list-section">
    <div class="shell">
        <div class="section-heading split-heading reveal"><div><span class="eyebrow">Coming up</span><h2>Upcoming events</h2></div><p>Save the date, get directions, download the event flyer or share the details directly on WhatsApp.</p></div>
        <?php if ($upcomingEvents === []): ?>
            <div class="content-empty reveal"><i class="fa-regular fa-calendar" aria-hidden="true"></i><h2>No upcoming events published</h2><p>Please check again soon for new dates and activities.</p></div>
        <?php else: ?>
            <div class="events-list"><?php foreach ($upcomingEvents as $event): $renderEventCard($event); endforeach; ?></div>
        <?php endif; ?>
    </div>
</section>

<?php if ($pastEvents !== []): ?>
<section class="section past-events-section">
    <div class="shell">
        <details class="archive-panel reveal"><summary><span><i class="fa-solid fa-clock-rotate-left" aria-hidden="true"></i><strong>Past events</strong><small>A dated record of completed and cancelled activities</small></span><i class="fa-solid fa-chevron-down" aria-hidden="true"></i></summary><div class="events-list archive-events"><?php foreach ($pastEvents as $event): $renderEventCard($event); endforeach; ?></div></details>
    </div>
</section>
<?php endif; ?>

<?php require PROJECT_ROOT . '/includes/footer.php'; ?>
