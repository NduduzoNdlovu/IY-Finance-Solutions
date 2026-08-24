<?php
declare(strict_types=1);

if (!defined('PROJECT_ROOT')) {
    require __DIR__ . '/config/bootstrap.php';
}
http_response_code(404);
$pageTitle = 'Page Not Found | ' . $site['name'];
$pageDescription = 'The requested page could not be found.';
$bodyClass = 'error-page';
require PROJECT_ROOT . '/includes/header.php';
?>
<section class="section error-section">
    <div class="shell error-card">
        <span>404</span>
        <h1>That page could not be found.</h1>
        <p>The address may have changed, or the page may no longer be available.</p>
        <a class="button button-primary" href="<?= h(site_url('index.php')) ?>">Return to the homepage</a>
    </div>
</section>
<?php require PROJECT_ROOT . '/includes/footer.php'; ?>

