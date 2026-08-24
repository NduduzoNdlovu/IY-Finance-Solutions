<?php
declare(strict_types=1);

require dirname(__DIR__) . '/config/bootstrap.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_is_valid($_POST['csrf_token'] ?? null)) {
    flash_message('error', 'The delete request could not be verified.');
    redirect_to('admin/');
}

$requestedFilename = (string) ($_POST['filename'] ?? '');
$filename = basename($requestedFilename);
$extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

if ($filename === '' || $filename !== $requestedFilename || !in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
    flash_message('error', 'The selected gallery file is not valid.');
    redirect_to('admin/');
}

$path = GALLERY_DIRECTORY . '/' . $filename;
$realPath = realpath($path);
$realDirectory = realpath(GALLERY_DIRECTORY);

if ($realPath === false || $realDirectory === false || dirname($realPath) !== $realDirectory || !is_file($realPath)) {
    flash_message('error', 'The selected image could not be found.');
    redirect_to('admin/');
}

if (!unlink($realPath)) {
    flash_message('error', 'The image could not be deleted. Check the gallery folder permissions.');
    redirect_to('admin/');
}

flash_message('success', 'The image was removed from the gallery.');
redirect_to('admin/');

