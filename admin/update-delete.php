<?php
declare(strict_types=1);

require dirname(__DIR__) . '/config/bootstrap.php';
require PROJECT_ROOT . '/config/content.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_is_valid($_POST['csrf_token'] ?? null)) {
    flash_message('error', 'The delete request could not be verified.');
    redirect_to('admin/updates.php');
}

$id = trim((string) ($_POST['id'] ?? ''));
$records = content_read_records(UPDATES_RECORD_FILE);
$record = content_find_by_id($records, $id);
if ($record === null) {
    flash_message('error', 'The selected update could not be found.');
    redirect_to('admin/updates.php');
}

$remaining = array_values(array_filter($records, static fn (array $item): bool => ($item['id'] ?? '') !== $id));
if (!content_write_records(UPDATES_RECORD_FILE, $remaining)) {
    flash_message('error', 'The update could not be deleted. Check the storage folder permissions.');
    redirect_to('admin/updates.php');
}

if (!empty($record['image'])) content_delete_image((string) $record['image'], UPDATES_UPLOAD_DIRECTORY);
flash_message('success', 'The update was deleted.');
redirect_to('admin/updates.php');

