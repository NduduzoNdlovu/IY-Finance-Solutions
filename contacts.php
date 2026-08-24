<?php
declare(strict_types=1);

require __DIR__ . '/config/bootstrap.php';
$branches = require PROJECT_ROOT . '/config/branches.php';

$pageTitle = 'Contact Us & Branch Locations | IY Finance Solutions';
$pageDescription = 'Contact IY Finance Solutions or find your nearest branch in KwaZulu-Natal, the Eastern Cape or Mpumalanga.';
$activePage = 'contact';
$bodyClass = 'contact-page';
$pageStyles = ['css/contact-page.css'];
$pageScript = 'contact-page.js';

$mapBranches = [];
foreach ($branches as $slug => $branch) {
    $mapBranches[] = [
        'slug' => $slug,
        'name' => $branch['name'],
        'city' => $branch['city'],
        'province' => $branch['province'],
        'provinceKey' => $branch['province_key'],
        'address' => $branch['address'],
        'phone' => $branch['phone'],
        'latitude' => $branch['latitude'],
        'longitude' => $branch['longitude'],
        'featured' => $branch['featured'],
    ];
}

require PROJECT_ROOT . '/includes/header.php';
?>

<section class="contact-hero">
    <div class="contact-hero-media" aria-hidden="true"></div>
    <div class="contact-hero-overlay" aria-hidden="true"></div>
    <div class="shell contact-hero-inner">
        <nav class="breadcrumbs" aria-label="Breadcrumb">
            <a href="<?= h(site_url('index.php')) ?>">Home</a>
            <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
            <span>Contact us</span>
        </nav>
        <div class="contact-hero-copy">
            <span class="eyebrow light">Here when you need us</span>
            <h1>Let’s start a clear, confidential conversation.</h1>
            <p>Speak to our team, send us a WhatsApp message or visit one of our eight branches across South Africa.</p>
            <div class="contact-hero-actions">
                <a class="button button-accent" href="tel:+27311400378">
                    <i class="fa-solid fa-phone" aria-hidden="true"></i> Call head office
                </a>
                <a class="button button-ghost" href="https://wa.me/27655711840?text=Hello%20IY%20Finance%20Solutions%2C%20I%20would%20like%20assistance." target="_blank" rel="noopener">
                    <i class="fa-brands fa-whatsapp" aria-hidden="true"></i> WhatsApp us
                </a>
            </div>
        </div>
        <aside class="contact-hero-card" aria-label="Head office contact details">
            <span class="contact-hero-card-label">Head office</span>
            <h2>Berea, Durban</h2>
            <a href="https://www.google.com/maps/search/?api=1&amp;query=<?= h(rawurlencode($branches['head-office']['address'])) ?>" target="_blank" rel="noopener">
                <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                <span><?= h($branches['head-office']['address']) ?></span>
            </a>
            <a href="mailto:<?= h($branches['head-office']['email']) ?>">
                <i class="fa-solid fa-envelope" aria-hidden="true"></i>
                <span><?= h($branches['head-office']['email']) ?></span>
            </a>
            <div>
                <i class="fa-regular fa-clock" aria-hidden="true"></i>
                <span><?= h($branches['head-office']['hours']) ?></span>
            </div>
        </aside>
    </div>
</section>

<section class="contact-shortcuts" aria-label="Quick contact options">
    <div class="shell contact-shortcuts-grid">
        <a href="tel:+27311400378">
            <span class="contact-shortcut-icon"><i class="fa-solid fa-phone-volume" aria-hidden="true"></i></span>
            <span><small>Call head office</small><strong>031 140 0378</strong></span>
            <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
        </a>
        <a href="mailto:enquiries@iyfinancesolutions.co.za">
            <span class="contact-shortcut-icon"><i class="fa-solid fa-envelope-open-text" aria-hidden="true"></i></span>
            <span><small>Email our team</small><strong>enquiries@iyfinancesolutions.co.za</strong></span>
            <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
        </a>
        <a href="https://wa.me/27655711840?text=Hello%20IY%20Finance%20Solutions%2C%20I%20would%20like%20assistance." target="_blank" rel="noopener">
            <span class="contact-shortcut-icon whatsapp"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i></span>
            <span><small>Chat on WhatsApp</small><strong>065 571 1840</strong></span>
            <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
        </a>
    </div>
</section>

<section class="section contact-locations-section" id="branches">
    <div class="shell">
        <div class="section-heading split-heading contact-locations-heading reveal">
            <div>
                <span class="eyebrow">Find your nearest branch</span>
                <h2>Personal support, closer to you</h2>
            </div>
            <p>Choose a province, explore the map and use the contact buttons to call, email, WhatsApp or get directions.</p>
        </div>

        <div class="branch-filters reveal" role="group" aria-label="Filter branches by province" data-branch-filters>
            <button class="is-active" type="button" data-province="all" aria-pressed="true">All locations <span>8</span></button>
            <button type="button" data-province="kwazulu-natal" aria-pressed="false">KwaZulu-Natal <span>5</span></button>
            <button type="button" data-province="eastern-cape" aria-pressed="false">Eastern Cape <span>1</span></button>
            <button type="button" data-province="mpumalanga" aria-pressed="false">Mpumalanga <span>2</span></button>
        </div>

        <div class="branch-map-wrap reveal">
            <div class="branch-map" id="branch-map" data-branch-map aria-label="Map showing all IY Finance Solutions branches"></div>
            <div class="branch-map-fallback" data-map-fallback>
                <i class="fa-solid fa-map-location-dot" aria-hidden="true"></i>
                <strong>Branch map</strong>
                <span>The interactive map needs an internet connection. Every branch and directions link is still available below.</span>
            </div>
            <div class="branch-map-key" aria-hidden="true">
                <span><i class="fa-solid fa-location-dot"></i> 8 locations</span>
                <span>Click a marker for branch details</span>
            </div>
        </div>

        <p class="branch-result-status" data-branch-status aria-live="polite">Showing all 8 locations</p>

        <div class="branch-grid" data-branch-grid>
            <?php foreach ($branches as $slug => $branch): ?>
                <?php
                $directionsUrl = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($branch['address']);
                $whatsAppMessage = rawurlencode('Hello IY Finance Solutions, I would like assistance from the ' . $branch['name'] . ' branch.');
                ?>
                <article
                    class="branch-card reveal<?= $branch['featured'] ? ' is-featured' : '' ?>"
                    id="branch-<?= h($slug) ?>"
                    data-branch-card
                    data-branch-slug="<?= h($slug) ?>"
                    data-province="<?= h($branch['province_key']) ?>"
                >
                    <header class="branch-card-header">
                        <span class="branch-pin"><i class="fa-solid fa-location-dot" aria-hidden="true"></i></span>
                        <div>
                            <span class="branch-province"><?= h($branch['province']) ?></span>
                            <h3><?= h($branch['name']) ?></h3>
                        </div>
                        <?php if ($branch['featured']): ?>
                            <span class="branch-featured-badge">Main office</span>
                        <?php endif; ?>
                    </header>

                    <a class="branch-address" href="<?= h($directionsUrl) ?>" target="_blank" rel="noopener">
                        <i class="fa-solid fa-diamond-turn-right" aria-hidden="true"></i>
                        <span><?= h($branch['address']) ?></span>
                    </a>

                    <div class="branch-contact-list">
                        <a href="tel:<?= h($branch['phone_link']) ?>">
                            <i class="fa-solid fa-phone" aria-hidden="true"></i>
                            <span><small>Telephone</small><strong><?= h($branch['phone']) ?></strong></span>
                        </a>
                        <a href="mailto:<?= h($branch['email']) ?>">
                            <i class="fa-solid fa-envelope" aria-hidden="true"></i>
                            <span><small><?= h($branch['email_label']) ?></small><strong><?= h($branch['email']) ?></strong></span>
                        </a>
                        <a href="https://wa.me/<?= h($branch['whatsapp_link']) ?>?text=<?= h($whatsAppMessage) ?>" target="_blank" rel="noopener">
                            <i class="fa-brands fa-whatsapp" aria-hidden="true"></i>
                            <span><small>Central WhatsApp</small><strong><?= h($branch['whatsapp']) ?></strong></span>
                        </a>
                        <?php if (!empty($branch['hours'])): ?>
                            <div>
                                <i class="fa-regular fa-clock" aria-hidden="true"></i>
                                <span><small>Office hours</small><strong><?= h($branch['hours']) ?></strong></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <footer class="branch-card-actions">
                        <a class="branch-directions" href="<?= h($directionsUrl) ?>" target="_blank" rel="noopener">
                            Get directions <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i>
                        </a>
                        <button type="button" data-show-on-map="<?= h($slug) ?>">
                            View on map <i class="fa-solid fa-map-location-dot" aria-hidden="true"></i>
                        </button>
                    </footer>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<script id="branch-map-data" type="application/json"><?= json_encode($mapBranches, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES) ?></script>

<?php require PROJECT_ROOT . '/includes/footer.php'; ?>
