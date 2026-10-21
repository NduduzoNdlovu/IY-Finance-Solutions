<?php
declare(strict_types=1);
?>

<div class="iy-promo" data-promo-popup hidden>
    <div class="iy-promo__backdrop" data-promo-close></div>

    <section class="iy-promo__card" role="dialog" aria-modal="true" aria-labelledby="iy-promo-title" aria-describedby="iy-promo-description">
        <button class="iy-promo__close" type="button" aria-label="Close promotion" data-promo-close>
            <span aria-hidden="true">&times;</span>
        </button>

        <div class="iy-promo__brand-panel">
            <img class="iy-promo__logo" src="<?= h(asset_url('images/logo/logoimg.webp')) ?>" alt="IY Finance Solutions">
            <p class="iy-promo__eyebrow">Were you declined for credit?</p>
            <h2 id="iy-promo-title">Let IY Finance Solutions help you understand your next step.</h2>
            <p id="iy-promo-description">If you were declined when applying for a vehicle, loan or other credit, send us your details and our team can contact you to discuss your situation.</p>

            <div class="iy-promo__actions">
                <a class="iy-promo__button iy-promo__button--primary" href="<?= h(site_url('contact.php')) ?>">
                    <i class="bi bi-geo-alt" aria-hidden="true"></i>
                    View branch near you
                </a>
                <a class="iy-promo__button iy-promo__button--secondary" href="<?= h(site_url('credit-assistance.php')) ?>">
                    <i class="bi bi-arrow-right-circle" aria-hidden="true"></i>
                    Get credit assistance
                </a>
            </div>
        </div>

        <div class="iy-promo__details">
            <div class="iy-promo__block">
                <p class="iy-promo__label"><i class="bi bi-shield-check" aria-hidden="true"></i> We can assist if you are under</p>
                <ul class="iy-promo__chips" aria-label="Debt-related services">
                    <li>Debt review</li>
                    <li>Admin order</li>
                    <li>Blacklisting</li>
                    <li>Defaults</li>
                    <li>Judgments</li>
                    <li>Garnishee order</li>
                </ul>
            </div>

            <div class="iy-promo__offer">
                <span>Personal loans</span>
                <strong>From R500 to R8&nbsp;000</strong>
                <small>Applications are subject to affordability assessment and approval.</small>
            </div>

            <div class="iy-promo__bottom-grid">
                <div class="iy-promo__block">
                    <p class="iy-promo__label"><i class="bi bi-file-earmark-check" aria-hidden="true"></i> Documents needed</p>
                    <ul class="iy-promo__documents">
                        <li>3-month bank statement</li>
                        <li>Latest payslip</li>
                        <li>Copy of ID</li>
                    </ul>
                </div>

                <div class="iy-promo__contact iy-promo__contact--action">
                    <strong>Need help after a declined application?</strong>
                    <span>Submit your contact details and tell us what you were trying to purchase or apply for.</span>
                    <a href="<?= h(site_url('credit-assistance.php')) ?>">Start credit assistance <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                </div>
            </div>
        </div>
    </section>
</div>
