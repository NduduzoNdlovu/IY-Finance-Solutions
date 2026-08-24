<?php
declare(strict_types=1);

require dirname(__DIR__) . '/config/bootstrap.php';
require PROJECT_ROOT . '/config/content.php';
require_admin();

$records = content_read_records(EVENTS_RECORD_FILE);
$id = trim((string) ($_GET['id'] ?? $_POST['id'] ?? ''));
$existing = $id !== '' ? content_find_by_id($records, $id) : null;
if ($id !== '' && $existing === null) {
    flash_message('error', 'The selected event could not be found.');
    redirect_to('admin/events.php');
}

$categories = content_event_categories();
$values = $existing ?? [
    'title' => '', 'category' => 'community', 'description' => '', 'start_date' => content_today(), 'start_time' => '',
    'end_date' => '', 'end_time' => '', 'location' => '', 'address' => '', 'status_override' => '',
    'gallery_url' => '', 'visible' => true, 'image' => '',
];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (['title', 'category', 'description', 'start_date', 'start_time', 'end_date', 'end_time', 'location', 'address', 'status_override', 'gallery_url'] as $field) {
        $values[$field] = trim((string) ($_POST[$field] ?? ''));
    }
    $values['visible'] = isset($_POST['visible']);

    if (!csrf_is_valid($_POST['csrf_token'] ?? null)) $errors[] = 'The form could not be verified. Refresh the page and try again.';
    if (content_text_length((string) $values['title']) < 3 || content_text_length((string) $values['title']) > 120) $errors[] = 'The title must be between 3 and 120 characters.';
    if (!isset($categories[$values['category']])) $errors[] = 'Select a valid event category.';
    if (content_text_length((string) $values['description']) < 20 || content_text_length((string) $values['description']) > 4000) $errors[] = 'The description must be between 20 and 4,000 characters.';
    if (!content_date_is_valid((string) $values['start_date'])) $errors[] = 'Enter a valid start date.';
    if (!content_time_is_valid((string) $values['start_time']) || !content_time_is_valid((string) $values['end_time'])) $errors[] = 'Enter valid event times.';
    if ($values['end_date'] !== '' && !content_date_is_valid((string) $values['end_date'])) $errors[] = 'Enter a valid end date or leave it blank.';
    if ($values['end_date'] !== '' && content_date_is_valid((string) $values['start_date']) && $values['end_date'] < $values['start_date']) $errors[] = 'The end date cannot be earlier than the start date.';
    $effectiveEndDate = $values['end_date'] !== '' ? $values['end_date'] : $values['start_date'];
    if ($effectiveEndDate === $values['start_date'] && $values['start_time'] !== '' && $values['end_time'] !== '' && $values['end_time'] <= $values['start_time']) $errors[] = 'For a one-day event, the end time must be later than the start time.';
    if (content_text_length((string) $values['location']) > 120 || content_text_length((string) $values['address']) > 240) $errors[] = 'The location or address is too long.';
    if (!in_array($values['status_override'], ['', 'cancelled', 'postponed'], true)) $errors[] = 'Select a valid event status.';
    if (!content_url_is_valid((string) $values['gallery_url'])) $errors[] = 'Use a complete http:// or https:// link for the optional gallery link.';

    $hasUpload = isset($_FILES['image']) && (int) ($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
    $newImage = null;
    if ($errors === [] && $hasUpload) {
        try { $newImage = content_upload_image($_FILES['image'], EVENTS_UPLOAD_DIRECTORY, 'event'); }
        catch (RuntimeException $exception) { $errors[] = $exception->getMessage(); }
    }

    if ($errors === []) {
        $now = gmdate('c');
        $record = [
            'id' => $existing['id'] ?? content_new_id('event'),
            'title' => $values['title'], 'category' => $values['category'], 'description' => $values['description'],
            'start_date' => $values['start_date'], 'start_time' => $values['start_time'],
            'end_date' => $values['end_date'], 'end_time' => $values['end_time'],
            'location' => $values['location'], 'address' => $values['address'],
            'image' => $newImage['filename'] ?? ($existing['image'] ?? ''),
            'status_override' => $values['status_override'], 'gallery_url' => $values['gallery_url'],
            'visible' => (bool) $values['visible'], 'created_at' => $existing['created_at'] ?? $now, 'updated_at' => $now,
        ];

        if ($existing === null) $records[] = $record;
        else foreach ($records as $index => $item) if (($item['id'] ?? '') === $existing['id']) { $records[$index] = $record; break; }

        if (content_write_records(EVENTS_RECORD_FILE, $records)) {
            if ($newImage !== null && !empty($existing['image'])) content_delete_image((string) $existing['image'], EVENTS_UPLOAD_DIRECTORY);
            flash_message('success', $existing === null ? 'The event was created successfully.' : 'The event was saved successfully.');
            redirect_to('admin/events.php');
        }
        if ($newImage !== null) content_delete_image((string) $newImage['filename'], EVENTS_UPLOAD_DIRECTORY);
        $errors[] = 'The event could not be saved. Check that the storage folder is writable.';
    }
}

$adminTitle = $existing === null ? 'Create event' : 'Edit event';
$adminPageStyles = ['css/content-admin.css'];
require PROJECT_ROOT . '/includes/admin-header.php';
?>

<section class="content-form-card">
    <div class="content-form-intro"><span class="admin-eyebrow"><?= $existing === null ? 'New event' : 'Edit event' ?></span><h1><?= $existing === null ? 'Add an event to the calendar' : 'Update event information' ?></h1><p>The date controls whether an event appears as upcoming, happening today or completed.</p></div>
    <?php if ($errors !== []): ?><div class="form-errors"><strong>Please correct the following:</strong><ul><?php foreach ($errors as $error): ?><li><?= h($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
    <form class="content-admin-form" method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>"><input type="hidden" name="id" value="<?= h((string) ($existing['id'] ?? '')) ?>"><input type="hidden" name="MAX_FILE_SIZE" value="8388608">
        <div class="form-grid">
            <label class="form-field full"><span>Event title *</span><input type="text" name="title" value="<?= h((string) $values['title']) ?>" maxlength="120" required></label>
            <label class="form-field"><span>Category *</span><select name="category" required><?php foreach ($categories as $key => $label): ?><option value="<?= h($key) ?>"<?= $values['category'] === $key ? ' selected' : '' ?>><?= h($label) ?></option><?php endforeach; ?></select></label>
            <label class="form-field"><span>Event flyer or photograph</span><input type="file" name="image" accept="image/jpeg,image/png,image/webp"><small>JPG, PNG or WEBP, maximum 8 MB.</small></label>
            <?php if (!empty($existing['image'])): ?><div class="current-image full"><img src="<?= h(content_media_url('event', (string) $existing['image'])) ?>" alt=""><span>Current image. Upload a new one only to replace it.</span></div><?php endif; ?>
            <label class="form-field full"><span>Description *</span><textarea name="description" maxlength="4000" required><?= h((string) $values['description']) ?></textarea></label>
            <label class="form-field"><span>Start date *</span><input type="date" name="start_date" value="<?= h((string) $values['start_date']) ?>" required></label>
            <label class="form-field"><span>Start time</span><input type="time" name="start_time" value="<?= h((string) $values['start_time']) ?>"></label>
            <label class="form-field"><span>End date</span><input type="date" name="end_date" value="<?= h((string) $values['end_date']) ?>"><small>Leave blank for a one-day event.</small></label>
            <label class="form-field"><span>End time</span><input type="time" name="end_time" value="<?= h((string) $values['end_time']) ?>"></label>
            <label class="form-field"><span>Venue or branch name</span><input type="text" name="location" value="<?= h((string) $values['location']) ?>" maxlength="120" placeholder="IY Durban branch"></label>
            <label class="form-field"><span>Street address</span><input type="text" name="address" value="<?= h((string) $values['address']) ?>" maxlength="240" placeholder="Full address for directions"></label>
            <label class="form-field"><span>Manual status</span><select name="status_override"><option value="">Automatic from date</option><option value="postponed"<?= $values['status_override'] === 'postponed' ? ' selected' : '' ?>>Postponed</option><option value="cancelled"<?= $values['status_override'] === 'cancelled' ? ' selected' : '' ?>>Cancelled</option></select><small>Normally leave automatic. Use only when an event is postponed or cancelled.</small></label>
            <label class="form-field"><span>Gallery link after the event</span><input type="url" name="gallery_url" value="<?= h((string) $values['gallery_url']) ?>" placeholder="https://www.iyfinancesolutions.co.za/gallery.php"><small>Add this after photographs have been published.</small></label>
        </div>
        <div><span class="form-section-title">Publishing option</span><div class="form-checks"><label class="form-check"><input type="checkbox" name="visible" value="1"<?= !empty($values['visible']) ? ' checked' : '' ?>><span>Visible to visitors<small>Untick to save the event as a hidden draft.</small></span></label></div></div>
        <div class="form-actions"><button class="admin-button primary" type="submit"><i class="fa-solid fa-floppy-disk" aria-hidden="true"></i> <?= $existing === null ? 'Create event' : 'Save changes' ?></button><a class="admin-button secondary" href="<?= h(site_url('admin/events.php')) ?>">Cancel</a></div>
    </form>
</section>

<?php require PROJECT_ROOT . '/includes/admin-footer.php'; ?>
