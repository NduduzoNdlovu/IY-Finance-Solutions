<?php
declare(strict_types=1);

require dirname(__DIR__) . '/config/bootstrap.php';
require PROJECT_ROOT . '/config/content.php';
require PROJECT_ROOT . '/config/referrals.php';
require_admin();
start_admin_session();

$errors = [];
$fullName = '';
$place = '';
$work = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_is_valid($_POST['csrf_token'] ?? null)) {
        flash_message('error', 'Your session expired. Please try again.');
        redirect_to('admin/referrals.php');
    }

    $action = trim((string) ($_POST['action'] ?? ''));

    if ($action === 'create') {
        $fullName = preg_replace('/\s+/u', ' ', trim((string) ($_POST['full_name'] ?? ''))) ?? '';
        $place = preg_replace('/\s+/u', ' ', trim((string) ($_POST['place'] ?? ''))) ?? '';
        $work = preg_replace('/\s+/u', ' ', trim((string) ($_POST['work'] ?? ''))) ?? '';

        if (function_exists('mb_strlen')) {
            if (mb_strlen($fullName, 'UTF-8') < 2 || mb_strlen($fullName, 'UTF-8') > 120) $errors[] = 'Enter a full name between 2 and 120 characters.';
            if (mb_strlen($place, 'UTF-8') < 2 || mb_strlen($place, 'UTF-8') > 120) $errors[] = 'Enter the place or area for this referral person.';
            if (mb_strlen($work, 'UTF-8') < 2 || mb_strlen($work, 'UTF-8') > 120) $errors[] = 'Enter their work, role or organisation.';
        } else {
            if (strlen($fullName) < 2 || strlen($fullName) > 120) $errors[] = 'Enter a full name between 2 and 120 characters.';
            if (strlen($place) < 2 || strlen($place) > 120) $errors[] = 'Enter the place or area for this referral person.';
            if (strlen($work) < 2 || strlen($work) > 120) $errors[] = 'Enter their work, role or organisation.';
        }

        if ($errors === []) {
            $referralId = referral_new_id();
            $record = [
                'referral_id' => $referralId,
                'full_name' => $fullName,
                'place' => $place,
                'work' => $work,
                'active' => true,
                'created_at' => gmdate('c'),
                'created_by' => (string) ($_SESSION['admin_username'] ?? 'admin'),
            ];

            $saved = referral_update_records(REFERRALS_FILE, static function (array $records) use ($record): array {
                $records[] = $record;
                return $records;
            });

            if ($saved) {
                flash_message('success', 'Referral profile created. The Referral ID is ' . $referralId . '. You can now open and print the assigned poster.');
                redirect_to('admin/referrals.php?poster=' . rawurlencode($referralId));
            }
            $errors[] = 'The referral profile could not be saved. Check that the storage folder is writable.';
        }
    } elseif ($action === 'toggle') {
        $referralId = strtoupper(trim((string) ($_POST['referral_id'] ?? '')));
        $changed = false;
        $saved = referral_update_records(REFERRALS_FILE, static function (array $records) use ($referralId, &$changed): array {
            foreach ($records as &$record) {
                if (($record['referral_id'] ?? '') === $referralId) {
                    $record['active'] = !($record['active'] ?? false);
                    $record['updated_at'] = gmdate('c');
                    $changed = true;
                    break;
                }
            }
            unset($record);
            return $records;
        });
        flash_message($saved && $changed ? 'success' : 'error', $saved && $changed ? 'Referral status updated.' : 'The referral status could not be updated.');
        redirect_to('admin/referrals.php');
    }
}

$referrals = referral_read_records(REFERRALS_FILE);
$scans = referral_read_records(REFERRAL_SCANS_FILE);
$enquiries = content_read_records(CREDIT_ENQUIRIES_FILE);
$posterId = strtoupper(trim((string) ($_GET['poster'] ?? '')));
$posterRecord = $posterId !== '' ? referral_find_by_id($posterId) : null;

$adminTitle = 'QR referrals and posters';
$adminPageStyles = ['css/referrals-admin.css'];
require PROJECT_ROOT . '/includes/admin-header.php';
?>

<section class="admin-shell referrals-page">
    <div class="dashboard-header referrals-heading">
        <div>
            <span class="admin-eyebrow">Marketing tools</span>
            <h1>QR referrals &amp; posters</h1>
            <p>Create a unique referral profile for each person, generate their tracking link, and print a branded IY Finance Solutions poster with their QR code.</p>
        </div>
        <div class="referral-total"><strong><?= count($referrals) ?></strong><span>Referral profiles</span></div>
    </div>

    <?php if ($errors !== []): ?>
        <div class="form-errors" role="alert"><strong>Please check the following:</strong><ul><?php foreach ($errors as $error): ?><li><?= h($error) ?></li><?php endforeach; ?></ul></div>
    <?php endif; ?>

    <div class="referrals-layout">
        <section class="admin-card referral-create-card">
            <div class="admin-card-heading">
                <span class="card-icon"><i class="fa-solid fa-user-plus" aria-hidden="true"></i></span>
                <div><h2>Create a referral profile</h2><p>Referral ID is generated automatically.</p></div>
            </div>
            <form method="post" action="<?= h(site_url('admin/referrals.php')) ?>" class="referral-form">
                <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                <input type="hidden" name="action" value="create">
                <label><span>Full name</span><input name="full_name" value="<?= h($fullName) ?>" maxlength="120" autocomplete="name" required placeholder="e.g. Patrick Mthembu"></label>
                <label><span>Place / area</span><input name="place" value="<?= h($place) ?>" maxlength="120" required placeholder="e.g. West Smith, Durban"></label>
                <label><span>Work / role / organisation</span><input name="work" value="<?= h($work) ?>" maxlength="120" required placeholder="e.g. Community referral partner"></label>
                <button class="admin-button primary" type="submit"><i class="fa-solid fa-qrcode" aria-hidden="true"></i> Create referral &amp; poster</button>
            </form>
            <p class="referral-help"><i class="fa-solid fa-circle-info" aria-hidden="true"></i> Each profile gets its own permanent referral URL. Scans and credit-assistance enquiries are attributed to that ID.</p>
        </section>

        <aside class="admin-card referral-guidance">
            <span class="card-icon soft"><i class="fa-solid fa-print" aria-hidden="true"></i></span>
            <h2>Print-ready marketing poster</h2>
            <p>The poster includes the IY Finance Solutions brand, the assigned person's full name, place, work details, auto-generated Referral ID and a QR code.</p>
            <ul>
                <li><i class="fa-solid fa-check" aria-hidden="true"></i> Designed for A4 printing</li>
                <li><i class="fa-solid fa-check" aria-hidden="true"></i> QR code points to the assigned referral link</li>
                <li><i class="fa-solid fa-check" aria-hidden="true"></i> Print directly from the browser</li>
                <li><i class="fa-solid fa-check" aria-hidden="true"></i> No customer identity is collected by a scan</li>
            </ul>
        </aside>
    </div>

    <?php if ($posterRecord !== null): ?>
        <section class="admin-card poster-ready">
            <div><span class="admin-eyebrow">Referral created</span><h2><?= h((string) $posterRecord['full_name']) ?>'s poster is ready</h2><p>Referral ID: <code><?= h((string) $posterRecord['referral_id']) ?></code></p></div>
            <a class="admin-button primary" href="<?= h(site_url('admin/referral-poster.php?id=' . rawurlencode((string) $posterRecord['referral_id']))) ?>"><i class="fa-solid fa-print" aria-hidden="true"></i> Open / print poster</a>
        </section>
    <?php endif; ?>

    <section class="admin-card referral-list-card">
        <div class="admin-card-heading">
            <span class="card-icon"><i class="fa-solid fa-chart-line" aria-hidden="true"></i></span>
            <div><h2>Referral performance</h2><p>Scan totals and enquiries saved against each referral ID.</p></div>
        </div>
        <div class="referral-table-wrap">
            <table class="referral-table">
                <thead><tr><th>Referral person</th><th>Place / work</th><th>Referral ID</th><th>Scans</th><th>Enquiries</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                <?php if ($referrals === []): ?>
                    <tr><td colspan="7" class="empty-cell">No referral profiles yet. Create the first one above.</td></tr>
                <?php endif; ?>
                <?php foreach (array_reverse($referrals) as $record): ?>
                    <?php
                    $rid = (string) ($record['referral_id'] ?? '');
                    $scanCount = count(array_filter($scans, static fn (array $scan): bool => ($scan['referral_id'] ?? '') === $rid));
                    $leadCount = count(array_filter($enquiries, static fn (array $lead): bool => ($lead['referral_id'] ?? '') === $rid));
                    ?>
                    <tr>
                        <td><strong><?= h((string) ($record['full_name'] ?? '')) ?></strong><small>Created <?= h(date('d M Y', strtotime((string) ($record['created_at'] ?? 'now')))) ?></small></td>
                        <td><?= h((string) ($record['place'] ?? '')) ?><small><?= h((string) ($record['work'] ?? '')) ?></small></td>
                        <td><code><?= h($rid) ?></code></td>
                        <td><?= number_format($scanCount) ?></td>
                        <td><?= number_format($leadCount) ?></td>
                        <td><span class="status-pill <?= !empty($record['active']) ? 'is-active' : 'is-inactive' ?>"><?= !empty($record['active']) ? 'Active' : 'Inactive' ?></span></td>
                        <td class="referral-actions">
                            <a class="admin-button small primary" href="<?= h(site_url('admin/referral-poster.php?id=' . rawurlencode($rid))) ?>"><i class="fa-solid fa-print" aria-hidden="true"></i> Poster</a>
                            <form method="post" action="<?= h(site_url('admin/referrals.php')) ?>">
                                <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                                <input type="hidden" name="action" value="toggle">
                                <input type="hidden" name="referral_id" value="<?= h($rid) ?>">
                                <button class="admin-button small <?= !empty($record['active']) ? 'danger' : 'secondary' ?>" type="submit"><?= !empty($record['active']) ? 'Deactivate' : 'Activate' ?></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p class="referral-footnote">A scan is recorded when a referral link opens. Repeat visits, messaging-app previews or automated link scanners may count as visits; scan totals are not guaranteed to represent unique people.</p>
    </section>
</section>

<?php require PROJECT_ROOT . '/includes/admin-footer.php'; ?>
