<?php
declare(strict_types=1);

require __DIR__ . '/config/bootstrap.php';
$pageTitle = 'About Us | IY Finance Solutions';
$pageDescription = 'Learn about IY Finance Solutions, our mission, values, professional standing and client-focused approach.';
$activePage = 'about';
$bodyClass = 'about-page';
require PROJECT_ROOT . '/includes/header.php';
?>

<section class="page-hero about-hero">

<!-- <section class="home-hero"> -->
    <!-- <div class="home-hero-media" aria-hidden="true"></div> -->
    <!-- <div class="home-hero-overlay" aria-hidden="true"></div> -->
    <div class="shell page-hero-inner">
        <nav class="breadcrumbs" aria-label="Breadcrumb"><a href="<?= h(site_url('index.php')) ?>">Home</a><i class="fa-solid fa-chevron-right" aria-hidden="true"></i><span>About us</span></nav>
        <span class="eyebrow light">About IY Finance Solutions</span>
        <h1>Experience, integrity and guidance that puts people first</h1>
        <p>We help South Africans understand their financial position and move forward with greater confidence.</p>
    </div>
</section>

<section class="section">
    <div class="shell story-layout">
        <div class="story-image reveal">
            <img src="<?= h(asset_url('images/prosper.png')) ?>" alt="Family discussing their financial future" width="1940" height="805">
            <div class="story-stamp"><span>Serving clients</span><strong>Since 2016</strong></div>
        </div>
        <div class="section-copy reveal">
            <span class="eyebrow">Our story</span>
            <h2>Financial support grounded in real experience</h2>
            <p><?= h($site['legal_name']) ?> was founded to make professional financial support more understandable and accessible.</p>
            <p>Our advisers bring experience from banking and financial services to every client conversation. That knowledge helps us explain complex requirements clearly, manage processes carefully and keep clients informed.</p>
            <p>We do not believe in one-size-fits-all advice. Every recommendation begins with listening to the client’s needs, responsibilities and goals.</p>
        </div>
    </div>
</section>

<section class="section soft-section">
    <div class="shell">
        <div class="section-heading centered reveal">
            <span class="eyebrow">What guides us</span>
            <h2>Built around trust and responsible service</h2>
        </div>
        <div class="values-grid">
            <article class="value-card reveal"><span><i class="fa-solid fa-bullseye" aria-hidden="true"></i></span><h3>Our mission</h3><p>To provide responsible financial guidance that helps individuals and families make informed decisions.</p></article>
            <article class="value-card featured reveal"><span><i class="fa-solid fa-eye" aria-hidden="true"></i></span><h3>Our vision</h3><p>To be a trusted financial partner known for integrity, accessibility and meaningful client outcomes.</p></article>
            <article class="value-card reveal"><span><i class="fa-solid fa-heart" aria-hidden="true"></i></span><h3>Our values</h3><p>Professionalism, honesty, confidentiality, responsibility and genuine client care.</p></article>
        </div>
    </div>
</section>

<section class="section credentials-section">
    <div class="shell credentials-layout">
        <div class="section-heading reveal">
            <span class="eyebrow light">Professional standing</span>
            <h2>Regulated. Accountable. Committed.</h2>
            <p>Our professional registrations reflect the standards and responsibilities that guide our work.</p>
        </div>
        <div class="credential-list reveal">
            <article><i class="fa-solid fa-shield-halved" aria-hidden="true"></i><div><strong>FSP <?= h($site['fsp']) ?></strong><span>Licensed Financial Services Provider</span></div></article>
            <article><i class="fa-solid fa-certificate" aria-hidden="true"></i><div><strong>NCRCP <?= h($site['ncrcp']) ?></strong><span>Registered Credit Provider</span></div></article>
            <article><i class="fa-solid fa-calendar-check" aria-hidden="true"></i><div><strong>Since 2016</strong><span>Serving South African consumers</span></div></article>
        </div>
    </div>
</section>

<section class="section">
    <div class="shell network-layout">
        <div class="section-copy reveal">
            <span class="eyebrow">Our reach</span>
            <h2>Support through a growing branch network</h2>
            <p>Our teams assist clients in person and through convenient remote communication channels.</p>
        </div>
    <div class="location-list reveal" aria-label="Service locations">
        <span><i class="fa-solid fa-location-dot" aria-hidden="true"></i>Berea (Head Office)</span>
        <span><i class="fa-solid fa-location-dot" aria-hidden="true"></i>Durban</span>
        <span><i class="fa-solid fa-location-dot" aria-hidden="true"></i>Pinetown</span>
        <span><i class="fa-solid fa-location-dot" aria-hidden="true"></i>Newcastle</span>
        <span><i class="fa-solid fa-location-dot" aria-hidden="true"></i>Pietermaritzburg</span>
        <span><i class="fa-solid fa-location-dot" aria-hidden="true"></i>Bizana</span>
        <span><i class="fa-solid fa-location-dot" aria-hidden="true"></i>Middelburg</span>
        <span><i class="fa-solid fa-location-dot" aria-hidden="true"></i>Witbank / eMalahleni</span>
    </div>
    </div>
</section>

<?php require PROJECT_ROOT . '/includes/footer.php'; ?>

