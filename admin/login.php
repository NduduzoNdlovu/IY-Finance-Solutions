<?php
declare(strict_types=1);

require dirname(__DIR__) . '/config/bootstrap.php';
start_admin_session();

if (!admin_is_configured()) {
    redirect_to('admin/setup.php');
}
if (admin_is_logged_in()) {
    redirect_to('admin/');
}

$error = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if (!csrf_is_valid($_POST['csrf_token'] ?? null)) {
        $error = 'Your session expired. Please try again.';
    } elseif (login_is_rate_limited()) {
        $error = 'Too many unsuccessful attempts. Please wait 15 minutes before trying again.';
    } else {
        $record = admin_record();
        $validPassword = $record !== null && password_verify($password, (string) $record['password_hash']);
        $validUsername = $record !== null && hash_equals((string) $record['username'], $username);

        if ($validUsername && $validPassword) {
            session_regenerate_id(true);
            $_SESSION['admin_authenticated'] = true;
            $_SESSION['admin_username'] = $record['username'];
            clear_failed_logins();
            redirect_to('admin/');
        }

        record_failed_login();
        $error = 'The username or password is incorrect.';
    }
}

$adminTitle = 'Administrator sign in';
require PROJECT_ROOT . '/includes/admin-header.php';
?>

<section class="auth-shell login-shell">
    <div class="auth-panel">
        <span class="auth-icon"><i class="fa-solid fa-lock" aria-hidden="true"></i></span>
        <span class="admin-eyebrow">Administrator access</span>
        <h1>Sign in to manage the gallery</h1>
        <p>Enter the administrator details created during the website setup.</p>

        <?php if ($error !== ''): ?><div class="form-errors" role="alert"><i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i><?= h($error) ?></div><?php endif; ?>

        <form class="auth-form" method="post" action="<?= h(site_url('admin/login.php')) ?>">
            <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
            <label><span>Username</span><input type="text" name="username" value="<?= h($username) ?>" autocomplete="username" required autofocus></label>
            <label><span>Password</span><input type="password" name="password" autocomplete="current-password" required></label>
            <button class="admin-button primary" type="submit"><i class="fa-solid fa-right-to-bracket" aria-hidden="true"></i> Sign in securely</button>
        </form>
    </div>
    <aside class="auth-aside"><i class="fa-regular fa-images" aria-hidden="true"></i><h2>Simple gallery management</h2><p>Upload several photographs at once, review published images and remove outdated content from one secure dashboard.</p></aside>
</section>

<?php require PROJECT_ROOT . '/includes/admin-footer.php'; ?>

