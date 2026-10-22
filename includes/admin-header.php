<?php
declare(strict_types=1);

$adminTitle = $adminTitle ?? 'Website administration';
$isAuthenticated = admin_is_logged_in();
$currentAdminPage = basename((string) (parse_url($_SERVER['SCRIPT_NAME'] ?? '', PHP_URL_PATH) ?: ''));
$adminNavigation = [
    ['file' => 'index.php', 'url' => 'admin/', 'label' => 'Dashboard', 'icon' => 'fa-solid fa-gauge-high'],
    ['file' => 'updates.php', 'url' => 'admin/updates.php', 'label' => 'News & updates', 'icon' => 'fa-solid fa-bullhorn'],
    ['file' => 'events.php', 'url' => 'admin/events.php', 'label' => 'Events calendar', 'icon' => 'fa-regular fa-calendar'],
    ['file' => 'credit-enquiries.php', 'url' => 'admin/credit-enquiries.php', 'label' => 'Credit enquiries', 'icon' => 'fa-solid fa-phone-volume'],
    ['file' => 'referrals.php', 'url' => 'admin/referrals.php', 'label' => 'QR referrals', 'icon' => 'fa-solid fa-qrcode'],
    ['file' => 'feedback.php', 'url' => 'admin/feedback.php', 'label' => 'Client feedback', 'icon' => 'fa-solid fa-comments'],
];
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
<body class="admin-body <?= $isAuthenticated ? 'has-admin-sidebar' : 'admin-guest' ?>">
<header class="admin-header">
    <div class="admin-topbar">
        <a class="admin-brand" href="<?= h(site_url('admin/')) ?>">
            <img src="<?= h(asset_url('images/logoimg.png')) ?>" alt="" width="44" height="44">
            <span><strong>IY Finance</strong><small>Website administration</small></span>
        </a>
        <?php if ($isAuthenticated): ?>
            <label class="admin-menu-toggle" for="admin-sidebar-toggle" aria-label="Toggle admin navigation">
                <i class="fa-solid fa-bars" aria-hidden="true"></i><span>Menu</span>
            </label>
        <?php endif; ?>
        <div class="admin-topbar-actions">
            <a href="<?= h(site_url('index.php')) ?>" target="_blank" rel="noopener">
                <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i><span>View website</span>
            </a>
        </div>
    </div>
</header>

<?php if ($isAuthenticated): ?>
<input class="admin-sidebar-toggle-input" type="checkbox" id="admin-sidebar-toggle" aria-label="Toggle admin navigation">
<aside class="admin-sidebar" aria-label="Administrator navigation">
    <div class="admin-sidebar-heading">WORKSPACE</div>
    <nav class="admin-sidebar-nav">
        <?php foreach ($adminNavigation as $item): ?>
            <a class="<?= $currentAdminPage === $item['file'] ? 'is-active' : '' ?>"
               href="<?= h(site_url($item['url'])) ?>"
               <?= $currentAdminPage === $item['file'] ? 'aria-current="page"' : '' ?>>
                <i class="<?= h($item['icon']) ?>" aria-hidden="true"></i><span><?= h($item['label']) ?></span>
            </a>
        <?php endforeach; ?>
    </nav>
    <div class="admin-sidebar-bottom">
        <span class="admin-sidebar-account"><i class="fa-solid fa-shield-halved" aria-hidden="true"></i> Admin access</span>
        <form action="<?= h(site_url('admin/logout.php')) ?>" method="post">
            <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
            <button type="submit"><i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i><span>Sign out</span></button>
        </form>
    </div>
</aside>
<?php endif; ?>

<div class="admin-workspace">
<?php foreach (consume_flash_messages() as $message): ?>
    <div class="admin-shell flash flash-<?= h($message['type'] ?? 'info') ?>" role="status">
        <i class="fa-solid <?= ($message['type'] ?? '') === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation' ?>" aria-hidden="true"></i>
        <span><?= h($message['message'] ?? '') ?></span>
    </div>
<?php endforeach; ?>

<main class="admin-main">
