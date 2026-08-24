<?php
declare(strict_types=1);

require dirname(__DIR__) . '/config/bootstrap.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_is_valid($_POST['csrf_token'] ?? null)) {
    flash_message('error', 'The upload request could not be verified. Please try again.');
    redirect_to('admin/');
}

if (!isset($_FILES['images']) || !is_array($_FILES['images']['name'] ?? null)) {
    flash_message('error', 'Please select at least one image to upload.');
    redirect_to('admin/');
}

$names = $_FILES['images']['name'];
$temporaryNames = $_FILES['images']['tmp_name'];
$errors = $_FILES['images']['error'];
$sizes = $_FILES['images']['size'];
$fileCount = count($names);

if ($fileCount > 12) {
    flash_message('error', 'You can upload a maximum of 12 images at a time.');
    redirect_to('admin/');
}

if (!is_dir(GALLERY_DIRECTORY) && !mkdir(GALLERY_DIRECTORY, 0755, true) && !is_dir(GALLERY_DIRECTORY)) {
    flash_message('error', 'The gallery folder could not be created. Check its cPanel permissions.');
    redirect_to('admin/');
}

if (!is_writable(GALLERY_DIRECTORY)) {
    flash_message('error', 'The gallery folder is not writable. Check its cPanel permissions.');
    redirect_to('admin/');
}

$allowedTypes = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/webp' => 'webp',
    'image/gif' => 'gif',
];
$maximumBytes = 8 * 1024 * 1024;
$uploaded = 0;
$rejected = [];
$finfo = new finfo(FILEINFO_MIME_TYPE);

for ($index = 0; $index < $fileCount; $index++) {
    $originalName = trim((string) ($names[$index] ?? 'image'));
    $temporaryName = (string) ($temporaryNames[$index] ?? '');
    $uploadError = (int) ($errors[$index] ?? UPLOAD_ERR_NO_FILE);
    $size = (int) ($sizes[$index] ?? 0);

    if ($uploadError !== UPLOAD_ERR_OK) {
        $rejected[] = $originalName . ' could not be uploaded.';
        continue;
    }
    if ($size <= 0 || $size > $maximumBytes) {
        $rejected[] = $originalName . ' must be smaller than 8 MB.';
        continue;
    }
    if (!is_uploaded_file($temporaryName)) {
        $rejected[] = $originalName . ' was not recognised as a valid upload.';
        continue;
    }

    $mimeType = $finfo->file($temporaryName);
    $dimensions = @getimagesize($temporaryName);
    if (!is_string($mimeType) || !isset($allowedTypes[$mimeType]) || $dimensions === false) {
        $rejected[] = $originalName . ' is not a supported image.';
        continue;
    }
    if ($dimensions[0] > 12000 || $dimensions[1] > 12000) {
        $rejected[] = $originalName . ' has dimensions that are too large.';
        continue;
    }

    $extension = $allowedTypes[$mimeType];
    $filename = gmdate('Ymd-His') . '-' . bin2hex(random_bytes(8)) . '.' . $extension;
    $destination = GALLERY_DIRECTORY . '/' . $filename;

    if (!move_uploaded_file($temporaryName, $destination)) {
        $rejected[] = $originalName . ' could not be saved.';
        continue;
    }

    @chmod($destination, 0644);
    $uploaded++;
}

if ($uploaded > 0) {
    flash_message('success', $uploaded . ($uploaded === 1 ? ' image was' : ' images were') . ' published successfully.');
}
if ($rejected !== []) {
    $preview = implode(' ', array_slice($rejected, 0, 3));
    if (count($rejected) > 3) {
        $preview .= ' ' . (count($rejected) - 3) . ' more files were rejected.';
    }
    flash_message('error', $preview);
}

redirect_to('admin/');

