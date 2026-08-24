<?php
declare(strict_types=1);

require dirname(__DIR__) . '/config/bootstrap.php';
require PROJECT_ROOT . '/config/content.php';
require_admin();

$records = content_read_records(UPDATES_RECORD_FILE);
$id = trim((string) ($_GET['id'] ?? $_POST['id'] ?? ''));
$existing = $id !== '' ? content_find_by_id($records, $id) : null;
if ($id !== '' && $existing === null) {
    flash_message('error', 'The selected update could not be found.');
    redirect_to('admin/updates.php');
}

$categories = content_update_categories();
$values = $existing ?? [
    'title' => '', 'category' => 'announcement', 'description' => '', 'published_on' => content_today(),
    'expires_on' => '', 'cta_label' => '', 'cta_url' => '', 'featured' => false, 'visible' => true, 'image' => '',
];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (['title', 'category', 'description', 'published_on', 'expires_on', 'cta_label', 'cta_url'] as $field) {
        $values[$field] = trim((string) ($_POST[$field] ?? ''));
    }
    $values['featured'] = isset($_POST['featured']);
    $values['visible'] = isset($_POST['visible']);

    if (!csrf_is_valid($_POST['csrf_token'] ?? null)) $errors[] = 'The form could not be verified. Refresh the page and try again.';
    if (content_text_length((string) $values['title']) < 3 || content_text_length((string) $values['title']) > 120) $errors[] = 'The title must be between 3 and 120 characters.';
    if (!isset($categories[$values['category']])) $errors[] = 'Select a valid update category.';
    if (content_text_length((string) $values['description']) < 20 || content_text_length((string) $values['description']) > 4000) $errors[] = 'The description must be between 20 and 4,000 characters.';
    if (!content_date_is_valid((string) $values['published_on'])) $errors[] = 'Enter a valid publication date.';
    if ($values['expires_on'] !== '' && !content_date_is_valid((string) $values['expires_on'])) $errors[] = 'Enter a valid expiry date or leave it blank.';
    if ($values['expires_on'] !== '' && content_date_is_valid((string) $values['published_on']) && $values['expires_on'] < $values['published_on']) $errors[] = 'The expiry date cannot be earlier than the publication date.';
    if (content_text_length((string) $values['cta_label']) > 45) $errors[] = 'The button label must be 45 characters or fewer.';
    if (!content_url_is_valid((string) $values['cta_url'])) $errors[] = 'Use a complete http://, https://, mailto: or tel: link for the optional button.';
    if ($values['cta_url'] !== '' && $values['cta_label'] === '') $values['cta_label'] = 'Learn more';

    $hasUpload = isset($_FILES['image']) && (int) ($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
    $newImage = null;
    if ($errors === [] && $hasUpload) {
        try { $newImage = content_upload_image($_FILES['image'], UPDATES_UPLOAD_DIRECTORY, 'update'); }
        catch (RuntimeException $exception) { $errors[] = $exception->getMessage(); }
    }

    if ($errors === []) {
        $now = gmdate('c');
        $recordId = $existing['id'] ?? content_new_id('update');
        $record = [
            'id' => $recordId,
            'title' => $values['title'],
            'slug' => $existing['slug'] ?? content_unique_slug((string) $values['title'], $records, (string) $recordId),
            'category' => $values['category'],
            'description' => $values['description'],
            'published_on' => $values['published_on'],
            'expires_on' => $values['expires_on'],
            'image' => $newImage['filename'] ?? ($existing['image'] ?? ''),
            'cta_label' => $values['cta_label'],
            'cta_url' => $values['cta_url'],
            'featured' => (bool) $values['featured'],
            'visible' => (bool) $values['visible'],
            'created_at' => $existing['created_at'] ?? $now,
            'updated_at' => $now,
        ];

        $saved = false;
        if ($existing === null) {
            $records[] = $record;
            $saved = content_write_records(UPDATES_RECORD_FILE, $records);
        } else {
            foreach ($records as $index => $item) if (($item['id'] ?? '') === $existing['id']) { $records[$index] = $record; break; }
            $saved = content_write_records(UPDATES_RECORD_FILE, $records);
        }

        if ($saved) {
            if ($newImage !== null && !empty($existing['image'])) content_delete_image((string) $existing['image'], UPDATES_UPLOAD_DIRECTORY);
            flash_message('success', $existing === null ? 'The update was created successfully.' : 'The update was saved successfully.');
            redirect_to('admin/updates.php');
        }
        if ($newImage !== null) content_delete_image((string) $newImage['filename'], UPDATES_UPLOAD_DIRECTORY);
        $errors[] = 'The update could not be saved. Check that the storage folder is writable.';
    }
}

$adminTitle = $existing === null ? 'Create update' : 'Edit update';
$adminPageStyles = ['css/content-admin.css'];
require PROJECT_ROOT . '/includes/admin-header.php';
?>

<section class="content-form-card">
    <div class="content-form-intro"><span class="admin-eyebrow"><?= $existing === null ? 'New post' : 'Edit post' ?></span><h1><?= $existing === null ? 'Create news or flyer' : 'Edit news or flyer' ?></h1><p>Use a clear title, accurate dates and an image that is easy to read on a phone.</p></div>
    <?php if ($errors !== []): ?><div class="form-errors"><strong>Please correct the following:</strong><ul><?php foreach ($errors as $error): ?><li><?= h($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
    <form class="content-admin-form" method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>"><input type="hidden" name="id" value="<?= h((string) ($existing['id'] ?? '')) ?>"><input type="hidden" name="MAX_FILE_SIZE" value="8388608">
        <div class="form-grid">
            <label class="form-field full"><span>Title *</span><input type="text" name="title" value="<?= h((string) $values['title']) ?>" maxlength="120" required></label>
            <label class="form-field"><span>Category *</span><select name="category" required><?php foreach ($categories as $key => $label): ?><option value="<?= h($key) ?>"<?= $values['category'] === $key ? ' selected' : '' ?>><?= h($label) ?></option><?php endforeach; ?></select></label>
            <label class="form-field"><span>Flyer or post image</span><input type="file" name="image" accept="image/jpeg,image/png,image/webp"><small>JPG, PNG or WEBP, maximum 8 MB. Leave blank while editing to keep the current image.</small></label>
            <?php if (!empty($existing['image'])): ?><div class="current-image full"><img src="<?= h(content_media_url('update', (string) $existing['image'])) ?>" alt=""><span>Current image. Upload a new one only if you want to replace it.</span></div><?php endif; ?>
            <label class="form-field full"><span>Description *</span><textarea name="description" maxlength="4000" required><?= h((string) $values['description']) ?></textarea><small>This text appears on the full post and is shortened automatically on cards.</small></label>
            <label class="form-field"><span>Publication date *</span><input type="date" name="published_on" value="<?= h((string) $values['published_on']) ?>" required></label>
            <label class="form-field"><span>Expiry date</span><input type="date" name="expires_on" value="<?= h((string) $values['expires_on']) ?>"><small>Expired posts move into the public archive. Leave blank for no expiry.</small></label>
            <label class="form-field"><span>Optional button label</span><input type="text" name="cta_label" value="<?= h((string) $values['cta_label']) ?>" maxlength="45" placeholder="Apply now"></label>
            <label class="form-field"><span>Optional button link</span><input type="url" name="cta_url" value="<?= h((string) $values['cta_url']) ?>" placeholder="https://..."></label>
        </div>
        <div><span class="form-section-title">Publishing options</span><div class="form-checks"><label class="form-check"><input type="checkbox" name="visible" value="1"<?= !empty($values['visible']) ? ' checked' : '' ?>><span>Visible to visitors<small>Untick to save as a hidden draft.</small></span></label><label class="form-check"><input type="checkbox" name="featured" value="1"<?= !empty($values['featured']) ? ' checked' : '' ?>><span>Featured update<small>Featured posts are placed first.</small></span></label></div></div>
        <div class="form-actions"><button class="admin-button primary" type="submit"><i class="fa-solid fa-floppy-disk" aria-hidden="true"></i> <?= $existing === null ? 'Create update' : 'Save changes' ?></button><a class="admin-button secondary" href="<?= h(site_url('admin/updates.php')) ?>">Cancel</a></div>
    </form>
</section>

<?php require PROJECT_ROOT . '/includes/admin-footer.php'; ?>
