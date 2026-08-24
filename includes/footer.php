<?php
declare(strict_types=1);

$servicesForFooter = require PROJECT_ROOT . '/config/services.php';
?>
</main>

<footer class="site-footer">
    <div class="footer-cta">
        <div class="shell footer-cta-inner">
            <div>
                <span class="eyebrow light">Start a confidential conversation</span>
                <h2>Let’s find your clearest next step.</h2>
            </div>
            <div class="footer-cta-actions">
                <a class="button button-light" href="tel:<?= h($site['phone_primary_link']) ?>"><i class="fa-solid fa-phone" aria-hidden="true"></i> Call <?= h($site['phone_primary']) ?></a>
                <a class="button button-outline-light" href="https://wa.me/<?= h($site['whatsapp_link']) ?>?text=Hello%20IY%20Finance%20Solutions%2C%20I%20would%20like%20assistance." target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> WhatsApp us</a>
            </div>
        </div>
    </div>

    <div class="shell footer-grid">
        <div class="footer-brand">
            <a class="brand brand-footer" href="<?= h(site_url('index.php')) ?>">
                <img src="<?= h(asset_url('images/logoimg.png')) ?>" alt="" width="54" height="54">
                <span><strong>Inqubeko Yezibusiso</strong><small> Finance Solutions</small></span>
            </a>
            <p><?= h($site['description']) ?></p>
            <div class="social-links" aria-label="Social media">
                <a href="<?= h($site['facebook']) ?>" target="_blank" rel="noopener" aria-label="Facebook"><i class="fa-brands fa-facebook-f" aria-hidden="true"></i></a>
                <a href="<?= h($site['twitter']) ?>" target="_blank" rel="noopener" aria-label="X"><i class="fa-brands fa-x-twitter" aria-hidden="true"></i></a>
                <a href="<?= h($site['linkedin']) ?>" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in" aria-hidden="true"></i></a>
            </div>
        </div>

        <div>
            <h3>Services</h3>
            <ul class="footer-links">
                <?php foreach ($servicesForFooter as $slug => $service): ?>
                    <li><a href="<?= h(site_url($slug . '.php')) ?>"><?= h($service['name']) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div>
            <h3>Company</h3>
            <ul class="footer-links">
                <li><a href="<?= h(site_url('about.php')) ?>">About us</a></li>
                <li><a href="<?= h(site_url('gallery.php')) ?>">Gallery</a></li>
                <li><a href="<?= h(site_url('index.php#process')) ?>">How it works</a></li>
                <li><a href="<?= h(site_url('updates.php')) ?>">News & updates</a></li>
<li><a href="<?= h(site_url('events.php')) ?>">Events calendar</a></li>
                <li><a href="<?= h(site_url('index.php#faq')) ?>">Frequently asked questions</a></li>
                <li><a href="<?= h(site_url('contact.php')) ?>">Contact us</a></li>
                <li><a href="<?= h(site_url('admin/')) ?>">Administrator</a></li>
            </ul>
        </div>

        <div>
            <h3>Contact</h3>
            <ul class="footer-contact">
                <li><i class="fa-solid fa-location-dot" aria-hidden="true"></i><span><?= h($site['address']) ?></span></li>
                <li><i class="fa-solid fa-phone" aria-hidden="true"></i><a href="tel:<?= h($site['phone_primary_link']) ?>"><?= h($site['phone_primary']) ?></a></li>
                <li><i class="fa-solid fa-envelope" aria-hidden="true"></i><a href="mailto:<?= h($site['email']) ?>"><?= h($site['email']) ?></a></li>
            </ul>
        </div>
    </div>

    <div class="shell footer-bottom">
        <p>&copy; <?= date('Y') ?> <?= h($site['legal_name']) ?>. All rights reserved.</p>
        <p>FSP <?= h($site['fsp']) ?> <span aria-hidden="true">•</span> NCRCP <?= h($site['ncrcp']) ?> <span aria-hidden="true">•</span> Products and services are subject to eligibility and applicable terms.</p>
        
        <p>Developed by <a href="https://www.iyfinance.co.za" target="_blank" rel="noopener">Nduduzo Ndlovu</a></p>
    </div>
</footer>

<a class="floating-whatsapp" href="https://wa.me/<?= h($site['whatsapp_link']) ?>?text=Hello%20IY%20Finance%20Solutions%2C%20I%20would%20like%20assistance." target="_blank" rel="noopener" aria-label="Chat with IY Finance Solutions on WhatsApp">
    <i class="fa-brands fa-whatsapp" aria-hidden="true"></i>
</a>

<script src="<?= h(asset_url('js/main.js')) ?>" defer></script>
<?php if (!empty($pageScript)): ?>
    <script src="<?= h(asset_url('js/' . $pageScript)) ?>" defer></script>
<?php endif; ?>
</body>
</html>
