<?php
declare(strict_types=1);

require dirname(__DIR__) . '/config/bootstrap.php';
require PROJECT_ROOT . '/config/content.php';
require_admin();

$images = gallery_images();
$adminTitle = 'Manage gallery';
$adminPageStyles = ['css/content-admin.css'];
$updatesTotal = count(content_read_records(UPDATES_RECORD_FILE));
$eventsTotal = count(content_read_records(EVENTS_RECORD_FILE));
require PROJECT_ROOT . '/includes/admin-header.php';
?>
<nav class="admin-shell admin-hub" aria-label="Website management areas">

    <article class="admin-hub-card">
        <i class="fa-regular fa-images" aria-hidden="true"></i>

        <div>
            <h2>Gallery</h2>

            <p>
                <?= count($images) ?>
                <?= count($images) === 1 ? 'image' : 'images' ?>
                currently published.
            </p>

            <a href="#gallery-management">
                Manage gallery
                <i class="fa-solid fa-arrow-down" aria-hidden="true"></i>
            </a>
        </div>
    </article>

    <article class="admin-hub-card">
        <i class="fa-solid fa-bullhorn" aria-hidden="true"></i>

        <div>
            <h2>News & Updates</h2>

            <p>
                <?= $updatesTotal ?>
                <?= $updatesTotal === 1 ? 'post' : 'posts' ?>
                created.
            </p>

            <a href="<?= h(site_url('admin/updates.php')) ?>">
                Manage updates
                <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
            </a>
        </div>
    </article>

    <article class="admin-hub-card">
        <i class="fa-regular fa-calendar" aria-hidden="true"></i>

        <div>
            <h2>Events Calendar</h2>

            <p>
                <?= $eventsTotal ?>
                <?= $eventsTotal === 1 ? 'event' : 'events' ?>
                recorded.
            </p>

            <a href="<?= h(site_url('admin/events.php')) ?>">
                Manage events
                <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
            </a>
        </div>
    </article>

</nav>

<div class="admin-shell dashboard-header" id="gallery-management">
<!-- <div class="admin-shell dashboard-header"> -->
    <div>
        <span class="admin-eyebrow">Gallery dashboard</span>
        <h1>Manage website photographs</h1>
        <p>Upload new pictures or remove images that should no longer appear in the public gallery.</p>
    </div>
    <div class="image-total"><strong><?= count($images) ?></strong><span><?= count($images) === 1 ? 'Image' : 'Images' ?> published</span></div>
</div>

<div class="admin-shell admin-layout">
    <section class="admin-card upload-card">
        <div class="admin-card-heading">
            <span class="card-icon"><i class="fa-solid fa-cloud-arrow-up" aria-hidden="true"></i></span>
            <div><h2>Upload photographs</h2><p>Add up to 12 images at a time.</p></div>
        </div>

        <form action="<?= h(site_url('admin/upload.php')) ?>" method="post" enctype="multipart/form-data" data-upload-form>
            <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
            <input type="hidden" name="MAX_FILE_SIZE" value="8388608">
            <label class="upload-dropzone" for="gallery-images" data-dropzone>
                <input id="gallery-images" type="file" name="images[]" accept="image/jpeg,image/png,image/webp,image/gif" multiple required data-file-input>
                <span><i class="fa-regular fa-images" aria-hidden="true"></i></span>
                <strong>Select gallery images</strong>
                <small>JPG, PNG, WEBP or GIF — maximum 8 MB each</small>
            </label>
            <div class="selected-files" data-selected-files aria-live="polite"></div>
            <button class="admin-button primary" type="submit" data-upload-button><i class="fa-solid fa-arrow-up-from-bracket" aria-hidden="true"></i> Upload selected images</button>
        </form>
    </section>

    <aside class="admin-card guidance-card">
        <span class="card-icon soft"><i class="fa-solid fa-wand-magic-sparkles" aria-hidden="true"></i></span>
        <h2>For the best results</h2>
        <ul>
            <li><i class="fa-solid fa-check" aria-hidden="true"></i>Use sharp, well-lit photographs.</li>
            <li><i class="fa-solid fa-check" aria-hidden="true"></i>Landscape and portrait images are both supported.</li>
            <li><i class="fa-solid fa-check" aria-hidden="true"></i>Confirm permission before publishing people’s photographs.</li>
            <li><i class="fa-solid fa-check" aria-hidden="true"></i>Compress very large files before uploading.</li>
        </ul>
    </aside>
</div>

<section class="admin-shell published-section">
    <div class="published-heading"><div><span class="admin-eyebrow">Published gallery</span><h2>Current photographs</h2></div><a href="<?= h(site_url('gallery.php')) ?>" target="_blank" rel="noopener">Open public gallery <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></a></div>

    <?php if ($images === []): ?>
        <div class="admin-empty"><i class="fa-regular fa-images" aria-hidden="true"></i><h3>No images have been published</h3><p>Use the upload area above to add the first gallery photographs.</p></div>
    <?php else: ?>
        <div class="admin-gallery">
            <?php foreach ($images as $image): ?>
                <article class="admin-image-card">
                    <img src="<?= h($image['url']) ?>" alt="Gallery photograph" width="<?= $image['width'] ?>" height="<?= $image['height'] ?>" loading="lazy">
                    <div>
                        <span><strong><?= h($image['filename']) ?></strong><small><?= h(format_bytes($image['size'])) ?> · <?= date('d M Y', $image['modified']) ?></small></span>
                        <form action="<?= h(site_url('admin/delete.php')) ?>" method="post" data-delete-form>
                            <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                            <input type="hidden" name="filename" value="<?= h($image['filename']) ?>">
                            <button type="submit" aria-label="Delete <?= h($image['filename']) ?>"><i class="fa-solid fa-trash-can" aria-hidden="true"></i></button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php require PROJECT_ROOT . '/includes/admin-footer.php'; ?>

