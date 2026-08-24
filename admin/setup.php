<?php
declare(strict_types=1);

require dirname(__DIR__) . '/config/bootstrap.php';
start_admin_session();

if (admin_is_configured()) {
    redirect_to(admin_is_logged_in() ? 'admin/' : 'admin/login.php');
}

$errors = [];
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $confirmation = (string) ($_POST['password_confirmation'] ?? '');

    if (!csrf_is_valid($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Your session expired. Please submit the form again.';
    }
    if (!preg_match('/^[A-Za-z0-9_-]{3,32}$/', $username)) {
        $errors[] = 'The username must contain 3–32 letters, numbers, hyphens or underscores.';
    }
    if (strlen($password) < 10) {
        $errors[] = 'The password must contain at least 10 characters.';
    }
    if (!preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $errors[] = 'Use at least one uppercase letter, one lowercase letter and one number.';
    }
    if ($password !== $confirmation) {
        $errors[] = 'The password confirmation does not match.';
    }

    if ($errors === [] && create_admin_record($username, $password)) {
        session_regenerate_id(true);
        $_SESSION['admin_authenticated'] = true;
        $_SESSION['admin_username'] = $username;
        flash_message('success', 'Administrator account created successfully.');
        redirect_to('admin/');
    }

    if ($errors === []) {
        $errors[] = 'The account could not be created. Confirm that the storage folder is writable.';
    }
}

$adminTitle = 'Create administrator';
require PROJECT_ROOT . '/includes/admin-header.php';
?>

<section class="auth-shell">
    <div class="auth-panel">
        <span class="auth-icon"><i class="fa-solid fa-user-shield" aria-hidden="true"></i></span>
        <span class="admin-eyebrow">One-time setup</span>
        <h1>Create the gallery administrator</h1>
        <p>This account will be able to upload and delete photographs. Setup closes automatically after the account is created.</p>

        <?php if ($errors !== []): ?>
            <div class="form-errors" role="alert"><strong>Please correct the following:</strong><ul><?php foreach ($errors as $error): ?><li><?= h($error) ?></li><?php endforeach; ?></ul></div>
        <?php endif; ?>

        <form class="auth-form" method="post" action="<?= h(site_url('admin/setup.php')) ?>">
            <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
            <label><span>Administrator username</span><input type="text" name="username" value="<?= h($username) ?>" minlength="3" maxlength="32" autocomplete="username" required></label>
            <label><span>Password</span><input type="password" name="password" minlength="10" autocomplete="new-password" required><small>At least 10 characters with uppercase, lowercase and a number.</small></label>
            <label><span>Confirm password</span><input type="password" name="password_confirmation" minlength="10" autocomplete="new-password" required></label>
            <button class="admin-button primary" type="submit"><i class="fa-solid fa-shield-halved" aria-hidden="true"></i> Create secure account</button>
        </form>
    </div>
    <aside class="auth-aside"><i class="fa-solid fa-lock" aria-hidden="true"></i><h2>Your gallery is protected</h2><p>Only someone with the administrator username and password can reach the upload and deletion controls.</p></aside>
</section>

<?php require PROJECT_ROOT . '/includes/admin-footer.php'; ?>

