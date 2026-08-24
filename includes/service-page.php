<?php
declare(strict_types=1);

$allServices = require PROJECT_ROOT . '/config/services.php';
$service = isset($serviceSlug) && is_string($serviceSlug)
    ? ($allServices[$serviceSlug] ?? null)
    : null;

if ($service === null) {
    http_response_code(404);
    require PROJECT_ROOT . '/404.php';
    exit;
}

$pageTitle = $service['name'] . ' | ' . $site['name'];
$pageDescription = $service['summary'];
$activePage = 'services';
$bodyClass = 'service-page service-' . $serviceSlug;


$heroImageUrl = asset_url('images/services/' . $service['hero_image']);
$heroPosition = $service['hero_position'] ?? 'center right';
$whatsappMessage = rawurlencode(
    'Hello IY Finance Solutions, I would like assistance with ' . $service['name'] . '.'
);
$whatsappUrl = 'https://wa.me/' . $site['whatsapp_link'] . '?text=' . $whatsappMessage;

require PROJECT_ROOT . '/includes/header.php';
?>

<section
    class="page-hero service-hero"
    style="--service-hero-image: url('<?= h($heroImageUrl) ?>'); --service-hero-position: <?= h($heroPosition) ?>;"
>
    <div class="shell page-hero-inner">
        <nav class="breadcrumbs" aria-label="Breadcrumb">
            <a href="<?= h(site_url('index.php')) ?>">Home</a>
            <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
            <span>Services</span>
            <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
            <span aria-current="page"><?= h($service['name']) ?></span>
        </nav>

        <div class="service-hero-content">
            <div class="service-hero-icon">
                <i class="fa-solid <?= h($service['icon']) ?>" aria-hidden="true"></i>
            </div>
            
            <span class="eyebrow light"><?= h($service['name']) ?></span>
            <h1><?= h($service['hero']) ?></h1>
            <p><?= h($service['lead']) ?></p>

            <div class="service-hero-actions">
                <a class="button button-light" href="<?= h(site_url('index.php#contact')) ?>">
                    Talk to an adviser
                    <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                </a>
                <a class="button button-outline-light" href="<?= h($whatsappUrl) ?>" target="_blank" rel="noopener">
                    <i class="fa-brands fa-whatsapp" aria-hidden="true"></i>
                    Ask on WhatsApp
                </a>
            </div>
        </div>
    </div>
</section>

<section class="section service-introduction">
    <div class="shell two-column-layout">
        <div class="section-copy reveal">
            <span class="eyebrow">Professional guidance</span>
            <h2><?= h($service['intro_title']) ?></h2>

            <?php foreach ($service['intro'] as $paragraph): ?>
                <p><?= h($paragraph) ?></p>
            <?php endforeach; ?>
        </div>

        <aside class="service-overview-card reveal" aria-label="<?= h($service['name']) ?> overview">
            <span class="service-overview-icon">
                <i class="fa-solid fa-shield-halved" aria-hidden="true"></i>
            </span>
            <h3><?= h($service['quick_title']) ?></h3>
            <ul>
                <?php foreach ($service['quick_points'] as $point): ?>
                    <li>
                        <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                        <span><?= h($point) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div class="service-credentials">
                <span>FSP <?= h($site['fsp']) ?></span>
                <span>NCRCP <?= h($site['ncrcp']) ?></span>
            </div>
        </aside>
    </div>
</section>

<section class="section soft-section service-process-section">
    <div class="shell">
        <div class="section-heading centered reveal">
            <span class="eyebrow">What to expect</span>
            <h2><?= h($service['feature_title']) ?></h2>
            <p>We keep every stage clear, practical and easy to understand.</p>
        </div>

        <div class="service-process-grid">
            <?php foreach ($service['features'] as $index => $feature): ?>
                <article class="service-process-card reveal">
                    <span class="feature-index">
                        <?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?>
                    </span>
                    <h3><?= h($feature[0]) ?></h3>
                    <p><?= h($feature[1]) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section service-preparation-section">
    <div class="shell">
        <div class="section-heading reveal">
            <span class="eyebrow">Prepare with confidence</span>
            <h2>Is this relevant to you—and what should you prepare?</h2>
            <p>Your first conversation is easier when you know the common situations and information involved.</p>
        </div>

        <div class="service-detail-grid">
            <article class="service-detail-card reveal">
                <div class="service-detail-heading">
                    <span><i class="fa-solid fa-user-check" aria-hidden="true"></i></span>
                    <h3><?= h($service['suitability_title']) ?></h3>
                </div>
                <ul class="service-detail-list">
                    <?php foreach ($service['suitability'] as $item): ?>
                        <li>
                            <i class="fa-solid fa-check" aria-hidden="true"></i>
                            <span><?= h($item) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </article>

            <article class="service-detail-card reveal">
                <div class="service-detail-heading">
                    <span><i class="fa-solid fa-folder-open" aria-hidden="true"></i></span>
                    <div>
                        <h3><?= h($service['documents_title']) ?></h3>
                        <p><?= h($service['documents_note']) ?></p>
                    </div>
                </div>
                <ul class="service-detail-list">
                    <?php foreach ($service['documents'] as $document): ?>
                        <li>
                            <i class="fa-solid fa-file-circle-check" aria-hidden="true"></i>
                            <span><?= h($document) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </article>
        </div>
    </div>
</section>

<section class="section service-outcome-section">
    <div class="shell">
        <div class="insight-card reveal">
            <div class="insight-icon">
                <i class="fa-solid fa-compass" aria-hidden="true"></i>
            </div>
            <div>
                <span class="eyebrow">The bigger picture</span>
                <h2><?= h($service['outcome_title']) ?></h2>
                <p><?= h($service['outcome']) ?></p>
            </div>
            <a class="button button-primary" href="<?= h(site_url('index.php#contact')) ?>">
                Contact our team
            </a>
        </div>

        <aside class="service-disclaimer reveal" aria-label="Important information">
            <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
            <div>
                <strong>Important information</strong>
                <p><?= h($service['disclaimer']) ?></p>
            </div>
        </aside>
    </div>
</section>

<section class="section soft-section service-faq-section">
    <div class="shell faq-layout">
        <div class="section-heading reveal">
            <span class="eyebrow">Questions, answered</span>
            <h2>What people ask about <?= h($service['name']) ?></h2>
            <p>These answers provide a useful starting point. Your circumstances and the applicable product or process may differ.</p>
            <a class="button button-primary" href="<?= h(site_url('index.php#contact')) ?>">
                Ask us a question
            </a>
        </div>

        <div class="faq-list" data-accordion>
            <?php foreach ($service['faq'] as $index => $faq): ?>
                <?php $faqId = 'service-faq-' . $serviceSlug . '-' . ($index + 1); ?>
                <article class="faq-item reveal">
                    <button
                        type="button"
                        aria-expanded="false"
                        aria-controls="<?= h($faqId) ?>"
                    >
                        <span><?= h($faq[0]) ?></span>
                        <i class="fa-solid fa-plus" aria-hidden="true"></i>
                    </button>
                    <div class="faq-answer" id="<?= h($faqId) ?>">
                        <p><?= h($faq[1]) ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section service-specific-cta">
    <div class="shell service-cta-card reveal">
        <div>
            <span class="eyebrow light">Speak to IY Finance Solutions</span>
            <h2><?= h($service['cta_title']) ?></h2>
            <p><?= h($service['cta_text']) ?></p>
        </div>
        <div class="service-cta-actions">
            <a class="button button-light" href="tel:<?= h($site['phone_primary_link']) ?>">
                <i class="fa-solid fa-phone" aria-hidden="true"></i>
                Call <?= h($site['phone_primary']) ?>
            </a>
            <a class="button button-outline-light" href="<?= h($whatsappUrl) ?>" target="_blank" rel="noopener">
                <i class="fa-brands fa-whatsapp" aria-hidden="true"></i>
                WhatsApp us
            </a>
        </div>
    </div>
</section>

<section class="section related-services soft-section">
    <div class="shell">
        <div class="section-heading reveal">
            <span class="eyebrow">Explore more</span>
            <h2>Other ways we can help</h2>
            <p>Explore another service or contact us if you are unsure which option is relevant.</p>
        </div>

        <div class="compact-services">
            <?php foreach ($allServices as $slug => $related): ?>
                <?php if ($slug === $serviceSlug) continue; ?>
                <a class="compact-service reveal" href="<?= h(site_url($slug . '.php')) ?>">
                    <i class="fa-solid <?= h($related['icon']) ?>" aria-hidden="true"></i>
                    <span><?= h($related['name']) ?></span>
                    <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require PROJECT_ROOT . '/includes/footer.php'; ?>
