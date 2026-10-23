<?php
declare(strict_types=1);

require __DIR__ . '/config/bootstrap.php';
require PROJECT_ROOT . '/config/content.php';
$services = require PROJECT_ROOT . '/config/services.php';
require PROJECT_ROOT . '/config/client-feedback.php';

$homeReviews = array_values(array_filter(client_feedback_read(), static function (array $record): bool {
    return ($record['status'] ?? '') === 'approved'
        && ($record['publication_consent'] ?? false) === true
        && trim((string) ($record['feedback'] ?? '')) !== '';
}));
usort($homeReviews, static fn (array $left, array $right): int => strcmp((string) ($right['reviewed_at'] ?? $right['created_at'] ?? ''), (string) ($left['reviewed_at'] ?? $left['created_at'] ?? '')));
$homeReviews = array_slice($homeReviews, 0, 3);
$pageTitle = 'IY Finance Solutions | Professional Financial Services in South Africa';
$pageDescription = 'IY Finance Solutions provides debt counselling, personal-loan guidance, funeral cover, legal insurance, credit-clearance support and financial assessments.';
$activePage = 'home';
$bodyClass = 'home-page';
$pageStyles = ['css/home-animations.css','css/client-feedback.css'];
$pageStyles[] = 'css/home-content-preview.css';
$pageScript = 'home-animations.js';
    $homeHeroImages = [
        //   'images/home-hero/confidence.png',
        'images/hero/family-advice.webp',
        'images/hero/hero-background.webp',
        'images/hero/couple-consultation.webp',
        'images/hero/entrepreneur-guidance.webp',
        'images/hero/family-security.webp',
        'images/hero/presentation.webp',
       
    ];
require PROJECT_ROOT . '/includes/header.php';
?>

<!-- <section class="home-hero">
    <div class="home-hero-media" aria-hidden="true"></div> -->
        <section class="home-hero">
        <video
            class="home-hero-media home-hero-video"
            autoplay
            muted
            loop
            playsinline
            preload="metadata"
            poster="<?= h(asset_url('images/hero/iy-office-loop-poster.webp')) ?>"
            tabindex="-1"
            aria-hidden="true"
            data-home-hero-video
        >
            <!-- <source src="<?= h(asset_url('videos/iy-office-loop.mp4')) ?>" type="video/mp4"> -->
        </video>

        <?php foreach ($homeHeroImages as $index => $homeHeroImage): ?>
            <div
                class="home-hero-media home-hero-slide<?= $index === 0 ? ' is-initial' : '' ?>"
                style="--home-hero-image: url('<?= h(asset_url($homeHeroImage)) ?>'); --home-hero-delay: <?= 7 + ($index * 6) ?>s;"
                aria-hidden="true"
            ></div>
        <?php endforeach; ?>
    <div class="home-hero-overlay" aria-hidden="true"></div>
    <div class="shell home-hero-inner">
        <div class="home-hero-designs" data-home-hero-designs aria-live="polite">
            <article class="home-hero-design is-active" data-home-hero-design="0">
                <div class="hero-copy">
                    <span class="hero-kicker"><i class="fa-solid fa-circle-check" aria-hidden="true"></i> Trusted financial guidance since 2016</span>
                    <h1>A clearer path to <em>financial freedom.</em></h1>
                    <p>Practical solutions, respectful support and straight answers for every step of your financial journey.</p>
                    <div class="hero-actions">
                        <a class="button button-accent" href="#services">Explore our services <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                        <a class="button button-ghost" href="https://wa.me/<?= h($site['whatsapp_link']) ?>?text=Hello%20IY%20Finance%20Solutions%2C%20I%20would%20like%20assistance." target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> Speak to our team</a>
                    </div>
                </div>

                <aside class="hero-trust-card" aria-label="Company credentials">
                    <span class="trust-label">Licensed. Registered. Client-focused.</span>
                    <div class="trust-row">
                        <strong>FSP <?= h($site['fsp']) ?></strong>
                        <span>Licensed financial services provider</span>
                    </div>
                    <div class="trust-row">
                        <strong>NCRCP <?= h($site['ncrcp']) ?></strong>
                        <span>Registered credit provider</span>
                    </div>
                    <div class="trust-row">
                        <strong>Since 2016</strong>
                        <span>Serving South African consumers</span>
                    </div>
                </aside>
            </article>

            <article class="home-hero-design home-hero-design--credit" data-home-hero-design="1" aria-hidden="true">
                <div class="hero-copy">
                    <span class="hero-kicker"><i class="fa-solid fa-hand-holding-heart" aria-hidden="true"></i> Credit assistance</span>
                    <h1>Declined for credit? <em>You may still have options.</em></h1>
                    <p>If you were declined for vehicle finance, a personal loan, home loan, retail credit, a cellphone contract or another credit application, tell us what happened and we will contact you to discuss your next step.</p>
                    <div class="hero-actions">
                        <a class="button button-accent" href="<?= h(site_url('credit-assistance.php')) ?>">Get Credit Assistance <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                        <a class="button button-ghost" href="https://wa.me/<?= h($site['whatsapp_link']) ?>?text=Hello%20IY%20Finance%20Solutions%2C%20I%20was%20declined%20for%20credit%20and%20would%20like%20assistance." target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> WhatsApp our team</a>
                    </div>
                </div>

                <aside class="hero-trust-card hero-credit-card" aria-label="Credit assistance information">
                    <span class="trust-label">We can start with a simple conversation</span>
                    <div class="trust-row">
                        <strong>Tell us what happened</strong>
                        <span>No need to know exactly why your application was declined.</span>
                    </div>
                    <div class="trust-row">
                        <strong>We contact you</strong>
                        <span>Give us your details and a consultant can follow up with you.</span>
                    </div>
                    <div class="trust-row">
                        <strong>Discuss your options</strong>
                        <span>We can guide the conversation from the information you provide.</span>
                    </div>
                </aside>
            </article>
        </div>

        <div class="home-hero-switcher" aria-label="Hero sections">
            <button type="button" class="home-hero-dot is-active" data-home-hero-dot="0" aria-label="Show main financial services introduction" aria-pressed="true"></button>
            <button type="button" class="home-hero-dot" data-home-hero-dot="1" aria-label="Show credit assistance introduction" aria-pressed="false"></button>
            <span class="home-hero-timer" aria-hidden="true"><span></span></span>
        </div>
    </div>
    <a class="scroll-cue" href="#services"><span>Discover more</span><i class="fa-solid fa-arrow-down" aria-hidden="true"></i></a>
</section>

<section class="home-solutions-marquee" aria-label="Explore our financial services">
    <div class="home-marquee-viewport">
        <div class="home-marquee-track">
            <div class="home-marquee-group">
                <?php foreach ($services as $slug => $service): ?>
                    <a class="home-marquee-item" href="<?= h(site_url($slug . '.php')) ?>">
                        <i class="fa-solid <?= h($service['icon']) ?>" aria-hidden="true"></i>
                        <span><?= h($service['name']) ?></span>
                        <i class="fa-solid fa-arrow-right-long" aria-hidden="true"></i>
                    </a>
                <?php endforeach; ?>
            </div>

            <div class="home-marquee-group" aria-hidden="true">
                <?php foreach ($services as $service): ?>
                    <span class="home-marquee-item">
                        <i class="fa-solid <?= h($service['icon']) ?>" aria-hidden="true"></i>
                        <span><?= h($service['name']) ?></span>
                        <i class="fa-solid fa-arrow-right-long" aria-hidden="true"></i>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<section class="section services-section" id="services">
    <div class="shell">
        <div class="section-heading split-heading reveal">
            <div>
                <span class="eyebrow">What we do</span>
                <h2>Financial support for real life</h2>
            </div>
            <p>Every financial situation is different. We help you understand your position and choose a responsible next step.</p>
        </div>

        <div class="services-grid">
            <?php foreach ($services as $slug => $service): ?>
                <article class="service-card reveal">
                    <div class="service-card-top">
                        <span class="service-card-icon"><i class="fa-solid <?= h($service['icon']) ?>" aria-hidden="true"></i></span>
                        <span class="service-card-number"><?= str_pad((string) (array_search($slug, array_keys($services), true) + 1), 2, '0', STR_PAD_LEFT) ?></span>
                    </div>
                    <h3><?= h($service['name']) ?></h3>
                    <p><?= h($service['summary']) ?></p>
                    <a href="<?= h(site_url($slug . '.php')) ?>">Learn more <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="trust-strip">
    <div class="shell trust-strip-grid">
        <div><i class="fa-solid fa-lock" aria-hidden="true"></i><span><strong>Confidential</strong> Your information is handled with care.</span></div>
        <div><i class="fa-solid fa-comments" aria-hidden="true"></i><span><strong>Clear</strong> We explain your options in plain language.</span></div>
        <div><i class="fa-solid fa-handshake" aria-hidden="true"></i><span><strong>Respectful</strong> Support without judgement or pressure.</span></div>
    </div>
</section>

<section class="section about-preview">
    <div class="shell media-layout">
        <div class="media-collage reveal">
            <img src="<?= h(asset_url('images/hero/family.webp')) ?>" alt="A South African family reviewing financial information together" width="1940" height="805" loading="lazy">
            <!-- <div class="experience-badge"><strong>10</strong><span>Years of trusted service</span></div> -->
       <div class="experience-badge"><strong data-count-to="9">9</strong><span>Years of trusted service</span></div>
        </div>
        <div class="section-copy reveal">
            <span class="eyebrow">Who we are</span>
            <h2>Professional experience with a human approach</h2>
            <p><?= h($site['legal_name']) ?> was built around a simple belief: people deserve financial guidance that is clear, respectful and suited to their circumstances.</p>
            <p>Our advisers combine financial-services experience with careful listening. We help clients understand the process, the paperwork and the practical impact of every option.</p>
            <ul class="check-list">
                <li><i class="fa-solid fa-circle-check" aria-hidden="true"></i> Solutions shaped around individual circumstances</li>
                <li><i class="fa-solid fa-circle-check" aria-hidden="true"></i> Clear communication at every stage</li>
                <li><i class="fa-solid fa-circle-check" aria-hidden="true"></i> Professional, confidential client care</li>
            </ul>
            <a class="text-link" href="<?= h(site_url('about.php')) ?>">Discover our story <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
        </div>
    </div>
</section>

<section class="section process-section" id="process">
    <div class="shell">
        <div class="section-heading centered reveal">
            <span class="eyebrow light">How it works</span>
            <h2>Four simple steps toward clarity</h2>
            <p>We keep the process understandable from your first conversation to the recommended next step.</p>
        </div>
        <div class="process-grid">
            <article class="process-card reveal">
                <span>01</span><i class="fa-solid fa-phone-volume" aria-hidden="true"></i>
                <h3>Start the conversation</h3>
                <p>Tell us what you need help with by phone, WhatsApp or email.</p>
            </article>
            <article class="process-card reveal">
                <span>02</span><i class="fa-solid fa-magnifying-glass-chart" aria-hidden="true"></i>
                <h3>Understand your position</h3>
                <p>We review the relevant information and ask the right questions.</p>
            </article>
            <article class="process-card reveal">
                <span>03</span><i class="fa-solid fa-route" aria-hidden="true"></i>
                <h3>Review your options</h3>
                <p>Your adviser explains the available paths, costs and considerations.</p>
            </article>
            <article class="process-card reveal">
                <span>04</span><i class="fa-solid fa-person-walking-arrow-right" aria-hidden="true"></i>
                <h3>Move forward confidently</h3>
                <p>Choose the next step that best suits your circumstances.</p>
            </article>
        </div>
    </div>
</section>

<?php if ($homeReviews !== []): ?>
<section class="section home-reviews-section" aria-labelledby="home-reviews-title">
    <div class="shell">
        <div class="section-heading split-heading reveal">
            <div>
                <span class="eyebrow">Client feedback</span>
                <h2 id="home-reviews-title">What our clients say</h2>
            </div>
            <p>Read experiences shared by clients who have given us permission to publish their feedback.</p>
        </div>
        <div class="reviews-grid home-reviews-grid">
            <?php foreach ($homeReviews as $review): ?>
                <article class="review-card reveal">
                    <div class="review-card-mark" aria-hidden="true"><i class="fa-solid fa-quote-left"></i></div>
                    <?php if (isset($review['rating']) && is_numeric($review['rating']) && (int) $review['rating'] >= 1 && (int) $review['rating'] <= 5): ?>
                        <div class="review-stars" aria-label="Rated <?= (int) $review['rating'] ?> out of 5 stars">
                            <?php for ($star = 1; $star <= 5; $star++): ?><i class="fa-solid fa-star<?= $star <= (int) $review['rating'] ? ' is-filled' : '' ?>" aria-hidden="true"></i><?php endfor; ?>
                        </div>
                    <?php endif; ?>
                    <blockquote><?= nl2br(h((string) $review['feedback'])) ?></blockquote>
                    <div class="review-card-footer">
                        <strong><?= !empty($review['display_name_consent']) && trim((string) ($review['name'] ?? '')) !== '' ? h((string) $review['name']) : 'Anonymous client' ?></strong>
                        <?php if (trim((string) ($review['service'] ?? '')) !== ''): ?><span><?= h((string) $review['service']) ?></span><?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        <div class="home-reviews-actions">
            <a class="button button-primary" href="<?= h(site_url('client-reviews.php')) ?>">View All Reviews <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
        </div>
        <p class="reviews-disclaimer">Testimonials reflect individual experiences and are not a guarantee of a particular credit, financial or service outcome.</p>
    </div>
</section>
<?php endif; ?>

<section class="section faq-section" id="faq">
    <div class="shell faq-layout">
        <div class="section-heading reveal">
            <span class="eyebrow">Questions, answered</span>
            <h2>Clarity starts with the right information</h2>
            <p>These are some of the questions people ask before speaking to our team.</p>
            <a class="button button-primary" href="#contact">Ask us a question</a>
        </div>
        <div class="faq-list" data-accordion>
            <article class="faq-item reveal">
                <button type="button" aria-expanded="false"><span>Is the first conversation confidential?</span><i class="fa-solid fa-plus" aria-hidden="true"></i></button>
                <div class="faq-answer"><p>Yes. We treat your personal and financial information confidentially and use it only for the purpose explained to you.</p></div>
            </article>
            <article class="faq-item reveal">
                <button type="button" aria-expanded="false"><span>How do I know which service is right for me?</span><i class="fa-solid fa-plus" aria-hidden="true"></i></button>
                <div class="faq-answer"><p>You do not need to decide before contacting us. Explain your circumstances and our team will help you understand which options may be relevant.</p></div>
            </article>
            <article class="faq-item reveal">
                <button type="button" aria-expanded="false"><span>What documents should I have ready?</span><i class="fa-solid fa-plus" aria-hidden="true"></i></button>
                <div class="faq-answer"><p>The exact documents depend on the service. Your adviser will provide a clear list, which may include identification, proof of income, bank statements or credit information.</p></div>
            </article>
            <article class="faq-item reveal">
                <button type="button" aria-expanded="false"><span>Are services available outside Durban?</span><i class="fa-solid fa-plus" aria-hidden="true"></i></button>
                <div class="faq-answer"><p>Yes. We support clients through our branch network and remote communication. Contact us and we will direct you to the most suitable team.</p></div>
            </article>
        </div>
    </div>
</section>
<?php require PROJECT_ROOT . '/includes/home-content-preview.php'; ?>
<section class="section contact-section" id="contact">
    <div class="shell contact-layout">
        <div class="contact-copy reveal">
            <span class="eyebrow light">Contact us</span>
            <h2>Let’s talk about your next step.</h2>
            <p>Choose the contact method that works for you. Our team will listen first and guide you from there.</p>
            <div class="contact-actions">
                <a class="button button-light" href="tel:<?= h($site['phone_primary_link']) ?>"><i class="fa-solid fa-phone" aria-hidden="true"></i> Call us now</a>
                <a class="button button-outline-light" href="https://wa.me/<?= h($site['whatsapp_link']) ?>?text=Hello%20IY%20Finance%20Solutions%2C%20I%20would%20like%20assistance." target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> WhatsApp</a>
            </div>
        </div>

        <div class="contact-cards reveal">
            <a href="tel:<?= h($site['phone_primary_link']) ?>">
                <i class="fa-solid fa-phone" aria-hidden="true"></i>
                <span><small>Call our team</small><strong><?= h($site['phone_primary']) ?></strong><em><?= h($site['phone_secondary']) ?></em></span>
            </a>
            <a href="mailto:<?= h($site['email']) ?>">
                <i class="fa-solid fa-envelope" aria-hidden="true"></i>
                <span><small>Email us</small><strong><?= h($site['email']) ?></strong></span>
            </a>
            <div>
                <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                <span><small>Durban head office</small><strong><?= h($site['address']) ?></strong></span>
            </div>
        </div>
    </div>
</section>
 <?php require PROJECT_ROOT . '/includes/promotion-popup.php'; ?>
<?php require PROJECT_ROOT . '/includes/footer.php'; ?>

