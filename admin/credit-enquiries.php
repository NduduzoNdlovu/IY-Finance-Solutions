<?php
declare(strict_types=1);

require dirname(__DIR__) . '/config/bootstrap.php';
require PROJECT_ROOT . '/config/content.php';
require_admin();

$allowedStatuses = [
    'new' => 'New',
    'called' => 'Called',
    'no-answer' => 'No Answer',
    'call-back' => 'Call Back',
    'active' => 'Active',
    'assisted' => 'Assisted',
    'not-interested' => 'Not Interested',
    'not-eligible' => 'Not Eligible',
    'closed' => 'Closed',
];

$records = content_read_records(CREDIT_ENQUIRIES_FILE);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_is_valid($_POST['csrf_token'] ?? null)) {
        flash_message('error', 'Your session expired. Please try again.');
        redirect_to('admin/credit-enquiries');
    }

    $id = trim((string) ($_POST['id'] ?? ''));
    $status = trim((string) ($_POST['status'] ?? ''));

    if ($id === '' || !isset($allowedStatuses[$status])) {
        flash_message('error', 'The enquiry update was not valid.');
        redirect_to('admin/credit-enquiries');
    }

    $updated = false;
    foreach ($records as &$record) {
        if (($record['id'] ?? '') !== $id) {
            continue;
        }
        $record['status'] = $status;
        $record['updated_at'] = gmdate('c');
        $updated = true;
        break;
    }
    unset($record);

    if (!$updated || !content_write_records(CREDIT_ENQUIRIES_FILE, $records)) {
        flash_message('error', 'The enquiry could not be updated.');
    } else {
        flash_message('success', 'Enquiry status updated.');
    }

    redirect_to('admin/credit-enquiries.php');
}

$search = trim((string) ($_GET['q'] ?? ''));
$statusFilter = trim((string) ($_GET['status'] ?? ''));

$filtered = array_values(array_filter($records, static function (array $record) use ($search, $statusFilter): bool {
    if ($statusFilter !== '' && ($record['status'] ?? 'new') !== $statusFilter) {
        return false;
    }

    if ($search === '') {
        return true;
    }

    $haystack = implode(' ', [
        (string) ($record['first_name'] ?? ''),
        (string) ($record['surname'] ?? ''),
        (string) ($record['phone'] ?? ''),
        (string) ($record['email'] ?? ''),
        (string) ($record['credit_type_label'] ?? ''),
    ]);

    return stripos($haystack, $search) !== false;
}));

usort($filtered, static function (array $left, array $right): int {
    return strcmp((string) ($right['created_at'] ?? ''), (string) ($left['created_at'] ?? ''));
});

$total = count($records);
$newCount = count(array_filter($records, static fn (array $record): bool => ($record['status'] ?? 'new') === 'new'));
$activeCount = count(array_filter($records, static fn (array $record): bool => ($record['status'] ?? '') === 'active'));
$assistedCount = count(array_filter($records, static fn (array $record): bool => ($record['status'] ?? '') === 'assisted'));

$adminTitle = 'Credit enquiries';
$adminPageStyles = ['css/credit-enquiries.css'];
require PROJECT_ROOT . '/includes/admin-header.php';
?>

<section class="admin-shell credit-enquiries-page">
    <div class="dashboard-header">
        <div>
            <span class="admin-eyebrow">Customer enquiries</span>
            <h1>Credit assistance enquiries</h1>
            <p>Review people who asked for help after being declined for credit and update their call status.</p>
        </div>
        <a class="admin-button primary" href="<?= h(site_url('credit-assistance.php')) ?>" target="_blank" rel="noopener"><i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i> Open enquiry form</a>
    </div>

    <div class="credit-stats">
        <article><span>Total enquiries</span><strong><?= $total ?></strong></article>
        <article><span>New</span><strong><?= $newCount ?></strong></article>
        <article><span>Active</span><strong><?= $activeCount ?></strong></article>
        <article><span>Assisted</span><strong><?= $assistedCount ?></strong></article>
    </div>

    <section class="admin-card credit-list-card">
        <form class="credit-filters" method="get" action="<?= h(site_url('admin/credit-enquiries.php')) ?>">
            <label><span>Search</span><input type="search" name="q" value="<?= h($search) ?>" placeholder="Name, phone or email"></label>
            <label><span>Status</span>
                <select name="status">
                    <option value="">All statuses</option>
                    <?php foreach ($allowedStatuses as $value => $label): ?>
                        <option value="<?= h($value) ?>" <?= $statusFilter === $value ? 'selected' : '' ?>><?= h($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <button class="admin-button primary" type="submit"><i class="fa-solid fa-filter" aria-hidden="true"></i> Filter</button>
            <?php if ($search !== '' || $statusFilter !== ''): ?><a class="credit-clear-filter" href="<?= h(site_url('admin/credit-enquiries.php')) ?>">Clear</a><?php endif; ?>
        </form>

        <?php if ($filtered === []): ?>
            <div class="credit-empty">
                <i class="fa-regular fa-folder-open" aria-hidden="true"></i>
                <h2>No enquiries found</h2>
                <p>New credit assistance enquiries will appear here.</p>
            </div>
        <?php else: ?>
            <div class="credit-table-wrap">
                <table class="credit-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Client</th>
                            <th>Contact</th>
                            <th>Looking for</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Call</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($filtered as $record): ?>
                        <?php
                        $status = (string) ($record['status'] ?? 'new');
                        $created = (string) ($record['created_at'] ?? '');
                        $createdLabel = $created !== '' ? date('d M Y H:i', strtotime($created)) : '—';
                        ?>
                        <tr>
                            <td data-label="Date"><?= h($createdLabel) ?></td>
                            <td data-label="Client"><strong><?= h(trim((string) ($record['first_name'] ?? '') . ' ' . (string) ($record['surname'] ?? ''))) ?></strong></td>
                            <td data-label="Contact">
                                <a class="credit-phone" href="tel:<?= h(preg_replace('/[^0-9+]/', '', (string) ($record['phone'] ?? ''))) ?>"><i class="fa-solid fa-phone" aria-hidden="true"></i> <?= h($record['phone'] ?? '') ?></a>
                                <?php if (!empty($record['email'])): ?><a class="credit-email" href="mailto:<?= h($record['email']) ?>"><?= h($record['email']) ?></a><?php endif; ?>
                            </td>
                            <td data-label="Looking for"><?= h($record['credit_type_label'] ?? $record['credit_type'] ?? '—') ?></td>
                            <td data-label="Reason"><?= h($record['decline_reason_label'] ?? $record['decline_reason'] ?? '—') ?></td>
                            <td data-label="Status">
                                <form class="credit-status-form" method="post" action="<?= h(site_url('admin/credit-enquiries')) ?>">
                                    <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                                    <input type="hidden" name="id" value="<?= h((string) ($record['id'] ?? '')) ?>">
                                    <select name="status" aria-label="Status for <?= h(trim((string) ($record['first_name'] ?? '') . ' ' . (string) ($record['surname'] ?? ''))) ?>" onchange="this.form.submit()">
                                        <?php foreach ($allowedStatuses as $value => $label): ?>
                                            <option value="<?= h($value) ?>" <?= $status === $value ? 'selected' : '' ?>><?= h($label) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </form>
                            </td>
                            <td data-label="Call"><a class="credit-call-button" href="tel:<?= h(preg_replace('/[^0-9+]/', '', (string) ($record['phone'] ?? ''))) ?>"><i class="fa-solid fa-phone-volume" aria-hidden="true"></i> Call</a></td>
                        </tr>
                        <?php if (!empty($record['message'])): ?>
                            <tr class="credit-message-row"><td colspan="7"><i class="fa-regular fa-message" aria-hidden="true"></i> <strong>Message:</strong> <?= h($record['message']) ?></td></tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</section>

<?php require PROJECT_ROOT . '/includes/admin-footer.php'; ?>
