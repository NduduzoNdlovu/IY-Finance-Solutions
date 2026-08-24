<?php
declare(strict_types=1);

$adminTitle = $adminTitle ?? 'Gallery administration';
$isAuthenticated = admin_is_logged_in();
?>
<!doctype html>
<html lang="en-ZA">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <meta name="theme-color" content="#073c2d">
    <title><?= h($adminTitle) ?> | <?= h($site['name']) ?></title>
    <link rel="icon" type="image/svg+xml" href="<?= h(asset_url('images/IY Finance.png')) ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="<?= h(asset_url('css/admin.css')) ?>">
    <?php foreach (($adminPageStyles ?? []) as $adminPageStyle): ?>
    <link rel="stylesheet" href="<?= h(asset_url($adminPageStyle)) ?>">
<?php endforeach; ?>
</head>
<body>
<header class="admin-header">
    <div class="admin-shell admin-nav">
        <a class="admin-brand" href="<?= h(site_url('admin/')) ?>">
            <img src="<?= h(asset_url('images/logoimg.png')) ?>" alt="" width="44" height="44">
            <span><strong>IY Finance</strong><small>Content administration</small></span>
        </a>
        <div class="admin-nav-actions">
            <a href="<?= h(site_url('gallery.php')) ?>" target="_blank" rel="noopener"><i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i> View gallery</a>
            <?php if ($isAuthenticated): ?>
                <a href="<?= h(site_url('admin/')) ?>">
    <i class="fa-regular fa-images" aria-hidden="true"></i> Gallery
</a>
<a href="<?= h(site_url('admin/updates.php')) ?>">
    <i class="fa-solid fa-bullhorn" aria-hidden="true"></i> Updates
</a>
<a href="<?= h(site_url('admin/events.php')) ?>">
    <i class="fa-regular fa-calendar" aria-hidden="true"></i> Events
</a>
                <form action="<?= h(site_url('admin/logout.php')) ?>" method="post">
                    <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                    <button type="submit"><i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i> Sign out</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</header>

<?php foreach (consume_flash_messages() as $message): ?>
    <div class="admin-shell flash flash-<?= h($message['type'] ?? 'info') ?>" role="status">
        <i class="fa-solid <?= ($message['type'] ?? '') === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation' ?>" aria-hidden="true"></i>
        <span><?= h($message['message'] ?? '') ?></span>
    </div>
<?php endforeach; ?>

<main class="admin-main">
