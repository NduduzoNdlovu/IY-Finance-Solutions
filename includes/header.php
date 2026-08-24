<?php
declare(strict_types=1);

$pageTitle = $pageTitle ?? $site['name'];
$pageDescription = $pageDescription ?? $site['description'];
$activePage = $activePage ?? '';
$bodyClass = $bodyClass ?? '';
$servicesForNavigation = require PROJECT_ROOT . '/config/services.php';
?>
<!doctype html>
<html lang="en-ZA">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= h($pageDescription) ?>">
    <meta name="theme-color" content="#073c2d">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= h($pageTitle) ?>">
    <meta property="og:description" content="<?= h($pageDescription) ?>">
    <meta property="og:image" content="<?= h(site_url('assets/images/og-image.jpg')) ?>">
    <title><?= h($pageTitle) ?></title>
    <link rel="icon" type="image/svg+xml" href="<?= h(asset_url('images/IY finance.png')) ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= h(asset_url('css/main.css')) ?>">
       <?php foreach (($pageStyles ?? []) as $pageStyle): ?>
        <link rel="stylesheet" href="<?= h(asset_url($pageStyle)) ?>">
    <?php endforeach; ?>
</head>
<body class="<?= h($bodyClass) ?>">
<a class="skip-link" href="#main-content">Skip to main content</a>

<header class="site-header" data-site-header>
    <div class="utility-bar">
        <div class="shell utility-inner">
            <p><i class="fa-solid fa-shield-halved" aria-hidden="true"></i> Licensed FSP <?= h($site['fsp']) ?> <span aria-hidden="true">•</span> NCRCP <?= h($site['ncrcp']) ?></p>
            <div class="utility-links">
                <a href="tel:<?= h($site['phone_primary_link']) ?>"><i class="fa-solid fa-phone" aria-hidden="true"></i><?= h($site['phone_primary']) ?></a>
                <a href="mailto:<?= h($site['email']) ?>"><i class="fa-solid fa-envelope" aria-hidden="true"></i><?= h($site['email']) ?></a>
            </div>
        </div>
    </div>

    <div class="main-nav-wrap">
        <div class="shell main-nav">
            <!-- <a class="brand" href="<?= h(site_url('index.php')) ?>" aria-label="IY Finance Solutions home">
                <img src="<?= h(asset_url('images/logoimg.png')) ?>" alt="" width="54" height="54">
                <span><strong>IY FINANCE</strong><span>SOLUTIONS</span></span>
            </a> -->
<a class="brand"
   href="<?= h(site_url('index.php')) ?>"
   aria-label="IY Finance Solutions home">

    <img src="<?= h(asset_url('images/logoimg.png')) ?>"
         alt=""
         width="54"
         height="54">

    <span class="brand-name-switcher" aria-hidden="true">

        <span class="brand-name brand-name-short">
            <strong>IY Finance</strong>
            <small>Solutions</small>
        </span>

        <span class="brand-name brand-name-full">
            <strong>Inqubeko Yezibusiso</strong>
            <small>Finance Solutions</small>
        </span>

    </span>
</a>
            <button class="nav-toggle" type="button" aria-controls="primary-navigation" aria-expanded="false" data-nav-toggle>
                <span class="sr-only">Open navigation</span>
                <span></span><span></span><span></span>
            </button>

            <nav class="primary-nav" id="primary-navigation" aria-label="Main navigation" data-nav>
                <a class="<?= $activePage === 'home' ? 'active' : '' ?>" href="<?= h(site_url('index.php')) ?>">
                    <i class="bi bi-house icon" aria-hidden="true"></i>
                HOME</a>
                <div class="nav-dropdown">
                    <button type="button" aria-expanded="false" data-dropdown-toggle>
                        <i class="bi bi-suitcase-lg icon" aria-hidden="true"></i>SERVICES <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
                    </button>
            
                    <div class="dropdown-menu">
    <?php foreach ($servicesForNavigation as $slug => $navService): ?>
        <a href="<?= h(site_url($slug . '.php')) ?>">
            <i class="fa-solid <?= h($navService['icon']) ?>" aria-hidden="true"></i>
            <span><?= h($navService['name']) ?></span>
        </a>
    <?php endforeach; ?>
</div>
                </div>
                <a class="<?= $activePage === 'about' ? 'active' : '' ?>" href="<?= h(site_url('about.php')) ?>">
                    <i class="bi bi-people icon" aria-hidden="true"></i>ABOUT</a>
                <a class="<?= $activePage === 'gallery' ? 'active' : '' ?>" href="<?= h(site_url('gallery.php')) ?>">
                    <i class="bi bi-images icon" aria-hidden="true"></i>GALLERY</a>
<a class="<?= in_array($activePage, ['updates', 'events'], true) ? 'active' : '' ?>"
   href="<?= h(site_url('updates.php')) ?>">
    <i class="bi bi-megaphone icon" aria-hidden="true"></i>
    UPDATES
</a>

                 <a class="<?= $activePage === 'contact' ? 'active' : '' ?>" href="<?= h(site_url('contact.php')) ?>">
                    <i class="bi bi-telephone icon" aria-hidden="true"></i>CONTACT</a>
                <a class="nav-cta" href="https://wa.me/<?= h($site['whatsapp_link']) ?>?text=Hello%20IY%20Finance%20Solutions%2C%20I%20would%20like%20assistance." target="_blank" rel="noopener">
                    <i class="fa-brands fa-whatsapp" aria-hidden="true"></i> Chat to us
                </a>
            </nav>
        </div>
    </div>
</header>

<main id="main-content">
