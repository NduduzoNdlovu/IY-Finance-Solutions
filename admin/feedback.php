<?php
declare(strict_types=1);

require dirname(__DIR__) . '/config/bootstrap.php';
require PROJECT_ROOT . '/config/client-feedback.php';
require_admin();

$records = client_feedback_read();
$statusLabels = ['pending' => 'Pending review', 'approved' => 'Approved', 'rejected' => 'Rejected'];
$adminTitle = 'Client feedback';
$adminPageStyles = ['css/admin-feedback.css'];

$search = trim((string) ($_GET['q'] ?? ''));
$statusFilter = trim((string) ($_GET['status'] ?? ''));
$filtered = array_values(array_filter($records, static function (array $record) use ($search, $statusFilter): bool {
    if ($statusFilter !== '' && ($record['status'] ?? 'pending') !== $statusFilter) {
        return false;
    }
    if ($search === '') {
        return true;
    }
    $haystack = implode(' ', [
        (string) ($record['name'] ?? ''),
        (string) ($record['email'] ?? ''),
        (string) ($record['service'] ?? ''),
        (string) ($record['language'] ?? ''),
        (string) ($record['feedback'] ?? ''),
    ]);
    return stripos($haystack, $search) !== false;
}));
usort($filtered, static fn (array $left, array $right): int => strcmp((string) ($right['created_at'] ?? ''), (string) ($left['created_at'] ?? '')));

$pendingCount = count(array_filter($records, static fn (array $r): bool => ($r['status'] ?? 'pending') === 'pending'));
$approvedCount = count(array_filter($records, static fn (array $r): bool => ($r['status'] ?? '') === 'approved'));
$consentCount = count(array_filter($records, static fn (array $r): bool => ($r['publication_consent'] ?? false) === true));

require PROJECT_ROOT . '/includes/admin-header.php';
?>
<section class="admin-shell feedback-admin-page">
    <div class="dashboard-header">
        <div>
            <span class="admin-eyebrow">Customer experience</span>
            <h1>Client feedback</h1>
            <p>Review feedback privately. Only publish testimonials when the client has given publication permission.</p>
        </div>
        <a class="admin-button" href="<?= h(site_url('client-reviews.php')) ?>" target="_blank" rel="noopener"><i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i> View public reviews</a>
    </div>

    <div class="feedback-admin-stats">
        <article><span>All submissions</span><strong><?= count($records) ?></strong></article>
        <article><span>Pending review</span><strong><?= $pendingCount ?></strong></article>
        <article><span>Approved</span><strong><?= $approvedCount ?></strong></article>
        <article><span>Publication consent</span><strong><?= $consentCount ?></strong></article>
    </div>

    <section class="admin-card feedback-admin-list">
        <form class="feedback-admin-filters" method="get" action="<?= h(site_url('admin/feedback.php')) ?>">
            <label><span>Search feedback</span><input type="search" name="q" value="<?= h($search) ?>" placeholder="Name, service, language or message"></label>
            <label><span>Status</span><select name="status"><option value="">All statuses</option><?php foreach ($statusLabels as $value => $label): ?><option value="<?= h($value) ?>" <?= $statusFilter === $value ? 'selected' : '' ?>><?= h($label) ?></option><?php endforeach; ?></select></label>
            <button class="admin-button primary" type="submit"><i class="fa-solid fa-filter" aria-hidden="true"></i> Filter</button>
            <?php if ($search !== '' || $statusFilter !== ''): ?><a class="feedback-admin-clear" href="<?= h(site_url('admin/feedback.php')) ?>">Clear</a><?php endif; ?>
        </form>

        <?php if ($filtered === []): ?>
            <div class="feedback-admin-empty"><i class="fa-regular fa-comments" aria-hidden="true"></i><h2>No feedback found</h2><p>New client submissions will appear here for private review.</p></div>
        <?php else: ?>
            <div class="feedback-admin-cards">
                <?php foreach ($filtered as $record): ?>
                    <article class="feedback-admin-record">
                        <header class="feedback-record-header">
                            <div>
                                <span class="feedback-status status-<?= h((string) ($record['status'] ?? 'pending')) ?>"><?= h($statusLabels[(string) ($record['status'] ?? 'pending')] ?? 'Pending review') ?></span>
                                <h2><?= h(trim((string) ($record['name'] ?? '')) !== '' ? (string) $record['name'] : 'Anonymous client') ?></h2>
                                <p><?= h((string) ($record['service'] ?? 'Service not specified')) ?><?= trim((string) ($record['language'] ?? '')) !== '' ? ' · ' . h((string) $record['language']) : '' ?> · <?= h((string) ($record['created_at'] ?? '')) ?></p>
                            </div>
                            <?php if (isset($record['rating']) && is_int($record['rating']) && $record['rating'] >= 1 && $record['rating'] <= 5): ?><span class="feedback-admin-rating"><i class="fa-solid fa-star" aria-hidden="true"></i> <?= (int) $record['rating'] ?>/5</span><?php else: ?><span class="feedback-admin-no-rating">No rating</span><?php endif; ?>
                        </header>
                        <div class="feedback-record-message"><?= nl2br(h((string) ($record['feedback'] ?? ''))) ?></div>
                        <dl class="feedback-record-meta">
                            <div><dt>Contact email</dt><dd><?= trim((string) ($record['email'] ?? '')) !== '' ? h((string) $record['email']) : 'Not provided' ?></dd></div>
                            <div><dt>Publication permission</dt><dd><?= !empty($record['publication_consent']) ? 'Yes' : 'No — keep private' ?></dd></div>
                            <div><dt>Name display permission</dt><dd><?= !empty($record['display_name_consent']) ? 'Yes, name may be shown' : 'No, display anonymously' ?></dd></div>
                        </dl>
                        <?php if (!empty($record['reviewed_at'])): ?><p class="feedback-record-review-note">Reviewed <?= h((string) $record['reviewed_at']) ?><?= !empty($record['reviewed_by']) ? ' by ' . h((string) $record['reviewed_by']) : '' ?>.</p><?php endif; ?>
                        <div class="feedback-record-actions">
                            <?php if (($record['status'] ?? 'pending') !== 'approved' && !empty($record['publication_consent'])): ?>
                                <form method="post" action="<?= h(site_url('admin/feedback-action.php')) ?>"><input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>"><input type="hidden" name="id" value="<?= h((string) $record['id']) ?>"><input type="hidden" name="action" value="approve"><button class="admin-button primary" type="submit"><i class="fa-solid fa-check" aria-hidden="true"></i> Approve & publish</button></form>
                            <?php elseif (($record['status'] ?? '') !== 'approved'): ?>
                                <span class="feedback-no-consent-note"><i class="fa-solid fa-lock" aria-hidden="true"></i> Publication consent is required before this can be published.</span>
                            <?php else: ?>
                                <span class="feedback-published-note"><i class="fa-solid fa-eye" aria-hidden="true"></i> Published on the public reviews page.</span>
                            <?php endif; ?>
                            <?php if (($record['status'] ?? '') !== 'rejected'): ?><form method="post" action="<?= h(site_url('admin/feedback-action.php')) ?>"><input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>"><input type="hidden" name="id" value="<?= h((string) $record['id']) ?>"><input type="hidden" name="action" value="reject"><button class="admin-button" type="submit"><i class="fa-solid fa-ban" aria-hidden="true"></i> Reject / unpublish</button></form><?php endif; ?>
                            <form method="post" action="<?= h(site_url('admin/feedback-action.php')) ?>" data-delete-form><input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>"><input type="hidden" name="id" value="<?= h((string) $record['id']) ?>"><input type="hidden" name="action" value="delete"><button class="admin-button danger" type="submit"><i class="fa-solid fa-trash-can" aria-hidden="true"></i> Delete</button></form>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</section>
<?php require PROJECT_ROOT . '/includes/admin-footer.php'; ?>
