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
$pageStyles = $serviceSlug === 'legal-insurance' ? ['css/legal-insurance.css'] : [];


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

<?php if ($serviceSlug === 'legal-insurance'): ?>
<section class="section legal-plans-section" id="legal-plans" aria-labelledby="legal-plans-title">
    <div class="shell">
        <div class="section-heading reveal">
            <span class="eyebrow">Find your level of legal support</span>
            <h2 id="legal-plans-title">Three IY Legal plans. One clearer choice.</h2>
            <p>Explore our three membership options, compare their benefits and choose the legal support that suits you.</p>
        </div>

        <div class="legal-plan-grid">
            <?php foreach ($service['legal_plans'] as $index => $plan): ?>
                <?php
                    $planMessage = rawurlencode('Hello IY Finance Solutions, I would like more information about the ' . $plan['name'] . ' Legal Plan.');
                    $planWhatsApp = 'https://wa.me/' . $site['whatsapp_link'] . '?text=' . $planMessage;
                ?>
                <article class="legal-plan-card reveal <?= $index === 1 ? 'is-featured' : '' ?>">
                    <div class="legal-plan-topline">
                        <span class="legal-plan-tier"><?= h($plan['tier']) ?></span>
                        <?php if ($plan['badge'] !== ''): ?>
                            <span class="legal-plan-badge"><?= h($plan['badge']) ?></span>
                        <?php endif; ?>
                    </div>

                    <h3><?= h($plan['name']) ?></h3>

                    <div class="legal-plan-price">
                        <strong><?= h($plan['price']) ?></strong>
                        <span> / month</span>
                    </div>

                    <p class="legal-plan-member">Monthly membership premium</p>

                    <div class="legal-plan-family">
                        <i class="fa-solid fa-people-group" aria-hidden="true"></i>
                        <span><strong><?= h($plan['family_fee']) ?></strong> / month for extended family</span>
                        <small><?= h($plan['family_age']) ?></small>
                    </div>

                    <ul class="legal-plan-features">
                        <?php foreach ($plan['features'] as $feature): ?>
                            <li>
                                <i class="fa-solid fa-check" aria-hidden="true"></i>
                                <span><?= h($feature) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <div class="legal-plan-extras">
                        <p>
                            <span>Bail benefit</span>
                            <strong><?= h($plan['bail']) ?></strong>
                        </p>
                        <p>
                            <span>Accidental-death legal cash benefit</span>
                            <strong><?= h($plan['death']) ?></strong>
                        </p>
                    </div>

                    <a class="button <?= $index === 1 ? 'button-accent' : 'button-primary' ?> legal-plan-cta"
                       href="<?= h($planWhatsApp) ?>"
                       target="_blank"
                       rel="noopener">
                        Enquire about this plan
                        <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                    </a>
                </article>
            <?php endforeach; ?>
        </div>

        <p class="legal-plan-footnote">
            Membership premiums and benefits are subject to the applicable IY Legal terms and conditions.
            Please contact our team to confirm the current premiums and membership requirements before joining.
        </p>
    </div>
</section>

<section class="section soft-section legal-comparison-section" aria-labelledby="legal-comparison-title">
    <div class="shell">
        <div class="section-heading reveal">
            <span class="eyebrow">Compare your benefits</span>
            <h2 id="legal-comparison-title">See how our plans compare</h2>
            <p>Take a closer look at the annual litigation benefits, waiting periods and additional legal benefits available with each membership level.</p>
        </div>

        <div class="legal-table-wrap reveal" role="region" aria-label="IY Legal plan comparison" tabindex="0">
            <table class="legal-comparison-table">
                <thead>
                    <tr>
                        <th scope="col">Benefit</th>
                        <th scope="col">Standard</th>
                        <th scope="col">Premium</th>
                        <th scope="col">Premium Plus</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($service['legal_comparison'] as $comparison): ?>
                        <tr>
                            <th scope="row"><?= h($comparison[0]) ?></th>
                            <td><?= h($comparison[1]) ?></td>
                            <td><?= h($comparison[2]) ?></td>
                            <td><?= h($comparison[3]) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <p class="legal-plan-footnote">
            Your membership commences after IY Legal receives your first premium.
            Waiting periods and benefit conditions apply. Contact us if you need clarification
            about commencement dates or eligibility for a particular benefit.
        </p>
    </div>
</section>

<section class="section legal-how-section">
    <div class="shell legal-how-grid">
        <div class="section-copy reveal">
            <span class="eyebrow">Your legal support matters</span>
            <h2>Professional guidance when legal challenges arise</h2>

            <p>
                Through IY Legal, you can access legal consultations and counselling,
                assistance with negotiations and correspondence with third parties,
                and guidance with basic legal documents.
            </p>

            <p>
                Our legal assistance also extends to qualifying debt-relief matters,
                including settlement negotiations, debt consolidations, payment extensions,
                blacklisting assistance and bankruptcy-related assistance.
            </p>

            <p>
                Qualifying matters can be referred to our network of advocates.
                Legal fees are handled per case, subject to your selected membership
                and the applicable authorisation requirements.
            </p>
        </div>

        <aside class="legal-exclusions-card reveal">
            <span class="legal-exclusions-icon">
                <i class="fa-solid fa-scale-balanced" aria-hidden="true"></i>
            </span>

            <h3>Understanding your legal cover</h3>

            <p>
                Certain legal matters fall outside the available membership benefits.
                These include:
            </p>

            <ul>
                <li>Matrimonial disputes</li>
                <li>Defamation cases</li>
                <li>Business-relations matters</li>
                <li>Admission-of-guilt fines</li>
                <li>Matters arising before membership or during applicable waiting periods</li>
                <li>Collective actions and traffic fines</li>
                <li>Political matters and child maintenance</li>
            </ul>

            <p class="legal-small-note">
                Additional exclusions and membership conditions apply.
                Contact our team for clarification before relying on a particular benefit.
            </p>
        </aside>
    </div>
</section>
<?php endif; ?>

<?php if ($serviceSlug === 'credit-clearance'): ?>
<section class="section credit-clearance-subservices" aria-labelledby="credit-clearance-options-title">
    <div class="shell">
        <div class="section-heading reveal">
            <span class="eyebrow">Credit profile solutions</span>
            <h2 id="credit-clearance-options-title">Let’s address what is holding your credit profile back</h2>
            <p>Negative listings and outdated information can stand in the way of your next financial step. IY Finance Solutions helps you review the record and follow the appropriate process to challenge, update or resolve eligible items.</p>
        </div>

        <?php
        $creditClearanceOptions = [
            [
                'icon' => 'fa-triangle-exclamation',
                'title' => 'Default (Adverse Listing)',
                'text' => 'Concerned about a default or adverse listing on your credit report? We can review the entry and help pursue removal or correction where the listing is inaccurate, outdated, settled or otherwise eligible under the applicable process.',
                'points' => ['Review the listing and supporting records', 'Check whether a dispute or update is appropriate'],
                'message' => 'I would like help reviewing a Default / Adverse Listing on my credit profile.',
            ],
            [
                'icon' => 'fa-handshake',
                'title' => 'Account Negotiations',
                'text' => 'If an account is affecting your credit standing, we can discuss available options for engaging with the credit provider, negotiating a resolution where possible, and requesting the correct account update once requirements are met.',
                'points' => ['Discuss possible settlement or resolution options', 'Help identify evidence needed for an account update'],
                'message' => 'I would like assistance with Account Negotiations for an account affecting my credit profile.',
            ],
            [
                'icon' => 'fa-scale-balanced',
                'title' => 'Judgements',
                'text' => 'A court judgement can have a serious effect on your credit profile. We can help you understand the records and documents involved and guide you through the relevant legal or administrative steps, including a rescission process where applicable.',
                'points' => ['Review available court and creditor documents', 'Explain the applicable rescission or record-update steps'],
                'message' => 'I would like assistance reviewing a judgement on my credit profile.',
            ],
            [
                'icon' => 'fa-file-circle-check',
                'title' => 'Disputes (Updating)',
                'text' => 'Still seeing incorrect, outdated or unpaid-looking information after a payment or change? We help prepare and follow up valid disputes and update requests with the relevant credit bureau or credit provider using the supporting documents available.',
                'points' => ['Prepare supporting information for a valid dispute', 'Follow up correction and paid-up or status updates'],
                'message' => 'I would like help with a credit bureau dispute or updating information on my credit profile.',
            ],
        ];
        ?>

        <div class="credit-clearance-options-grid">
            <?php foreach ($creditClearanceOptions as $index => $option): ?>
                <?php $optionWhatsApp = 'https://wa.me/' . $site['whatsapp_link'] . '?text=' . rawurlencode('Hello IY Finance Solutions, ' . $option['message']); ?>
                <article class="credit-clearance-option-card reveal">
                    <div class="credit-clearance-option-top">
                        <span class="credit-clearance-option-icon"><i class="fa-solid <?= h($option['icon']) ?>" aria-hidden="true"></i></span>
                        <span class="credit-clearance-option-number"><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></span>
                    </div>
                    <h3><?= h($option['title']) ?></h3>
                    <p><?= h($option['text']) ?></p>
                    <ul>
                        <?php foreach ($option['points'] as $point): ?>
                            <li><i class="fa-solid fa-check" aria-hidden="true"></i><span><?= h($point) ?></span></li>
                        <?php endforeach; ?>
                    </ul>
                    <a class="text-link" href="<?= h($optionWhatsApp) ?>" target="_blank" rel="noopener">Ask about this service <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                </article>
            <?php endforeach; ?>
        </div>

        <p class="credit-clearance-note">Every case is assessed individually. We do not promise automatic removal of accurate information. Any removal, correction or update depends on the evidence, the relevant credit provider or bureau, and the applicable legal process.</p>
        <div class="credit-clearance-bottom-cta">
            <div><h3>Not sure which service you need?</h3><p>Let our team review your concern and explain the next step for your circumstances.</p></div>
            <a class="button button-primary" href="<?= h(site_url('index.php#contact')) ?>">Request assistance <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($serviceSlug !== 'legal-insurance'): ?>
<section class="section soft-section service-process-section">
    <div class="shell">
        <div class="section-heading  reveal">
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
<?php endif; ?>

<?php if ($serviceSlug !== 'legal-insurance'): ?>
<section class="section service-preparation-section">
    <div class="shell">
        <div class="section-heading reveal">
            <span class="eyebrow">Prepare with confidence</span>
            <h2>Is this relevant to you and what should you prepare?</h2>
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
<?php endif; ?>

<?php if ($serviceSlug !== 'legal-insurance'): ?>
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
<?php endif; ?>

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
