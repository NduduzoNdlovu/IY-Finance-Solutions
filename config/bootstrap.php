<?php
declare(strict_types=1);

define('PROJECT_ROOT', dirname(__DIR__));
define('GALLERY_DIRECTORY', PROJECT_ROOT . '/uploads/gallery');
define('STORAGE_DIRECTORY', PROJECT_ROOT . '/storage');
define('ADMIN_RECORD_FILE', STORAGE_DIRECTORY . '/admin.json');

$site = [
    'name' => 'IY Finance Solutions',
    'legal_name' => 'Inqubeko Yezibusiso Finance Solutions (Pty) Ltd',
    'tagline' => 'A clearer path to financial freedom.',
    'description' => 'Professional financial guidance and practical solutions for South African individuals and families.',
    'email' => 'info@iyfinancesolutions.co.za',
    // 'phone_primary' => '+27 31 029 8982',
    // 'phone_primary_link' => '+27310298982',
    // 'phone_secondary' => '+27 31 140 0378',
    // 'phone_secondary_link' => '+27311400378',
    'whatsapp' => '+27 65 571 1840',
    'whatsapp_link' => '27655711840',
    // 'address' => '320 Dr Pixley Ka Seme Street, Redefine Building, 3rd Floor, Office 301D, Durban, 4001',
    //  'email' => 'enquiries@iyfinancesolutions.co.za',
    'phone_primary' => '+27 31 140 0378',
    'phone_primary_link' => '+27311400378',
    'phone_secondary' => '+27 31 029 8982',
    'phone_secondary_link' => '+27310298982',
    'address' => '281 Florida Road, Unit 18 Hacienda, Berea, 4001',
    'fsp' => '49179',
    'ncrcp' => '8769',
    'facebook' => 'https://www.facebook.com/iyfinancesolutions.co.za',
    'twitter' => 'https://twitter.com/IYFinSolutions',
    'linkedin' => 'https://www.linkedin.com/company/iy-finance-solution/',
];

function h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function base_path(): string
{
    static $base = null;

    if ($base !== null) {
        return $base;
    }

    $configured = getenv('IY_BASE_PATH');
    if ($configured !== false && trim($configured) !== '') {
        $base = '/' . trim((string) $configured, '/');
        return $base === '/' ? '' : $base;
    }

    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/index.php');
    $directory = rtrim(str_replace('\\', '/', dirname($script)), '/');

    if (preg_match('#^(.*)/admin$#', $directory, $matches)) {
        $directory = $matches[1];
    }

    $base = ($directory === '' || $directory === '.') ? '' : $directory;
    return $base;
}

function site_url(string $path = ''): string
{
    $path = ltrim($path, '/');
    return base_path() . ($path === '' ? '/' : '/' . $path);
}

function asset_url(string $path): string
{
    $absolutePath = PROJECT_ROOT . '/assets/' . ltrim($path, '/');
    $version = is_file($absolutePath) ? (string) filemtime($absolutePath) : '1';
    return site_url('assets/' . ltrim($path, '/')) . '?v=' . rawurlencode($version);
}

function redirect_to(string $path): never
{
    header('Location: ' . site_url($path), true, 302);
    exit;
}

function send_security_headers(): void
{
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
    header('X-Frame-Options: SAMEORIGIN');
}

function gallery_images(): array
{
    if (!is_dir(GALLERY_DIRECTORY)) {
        return [];
    }

    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    $images = [];
    $iterator = new DirectoryIterator(GALLERY_DIRECTORY);

    foreach ($iterator as $file) {
        if (!$file->isFile() || $file->isDot()) {
            continue;
        }

        $extension = strtolower($file->getExtension());
        if (!in_array($extension, $allowedExtensions, true)) {
            continue;
        }

        $dimensions = @getimagesize($file->getPathname());
        if ($dimensions === false) {
            continue;
        }

        $filename = $file->getFilename();
        $images[] = [
            'filename' => $filename,
            'url' => site_url('uploads/gallery/' . rawurlencode($filename)),
            'width' => (int) $dimensions[0],
            'height' => (int) $dimensions[1],
            'modified' => $file->getMTime(),
            'size' => $file->getSize(),
        ];
    }

    usort($images, static function (array $left, array $right): int {
        if ($left['modified'] === $right['modified']) {
            return strnatcasecmp($right['filename'], $left['filename']);
        }
        return $right['modified'] <=> $left['modified'];
    });

    return $images;
}

function format_bytes(int $bytes): string
{
    if ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 1) . ' MB';
    }
    return number_format($bytes / 1024, 0) . ' KB';
}

function start_admin_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    $secure = !empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off';
    session_name('iy_admin_session');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => base_path() === '' ? '/' : base_path() . '/',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Strict',
    ]);
    session_start();
}

function csrf_token(): string
{
    start_admin_session();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return (string) $_SESSION['csrf_token'];
}

function csrf_is_valid(?string $token): bool
{
    start_admin_session();
    return is_string($token)
        && isset($_SESSION['csrf_token'])
        && hash_equals((string) $_SESSION['csrf_token'], $token);
}

function admin_record(): ?array
{
    if (!is_file(ADMIN_RECORD_FILE)) {
        return null;
    }

    $record = json_decode((string) file_get_contents(ADMIN_RECORD_FILE), true);
    if (!is_array($record) || empty($record['username']) || empty($record['password_hash'])) {
        return null;
    }
    return $record;
}

function admin_is_configured(): bool
{
    return admin_record() !== null;
}

function create_admin_record(string $username, string $password): bool
{
    if (admin_is_configured()) {
        return false;
    }

    if (!is_dir(STORAGE_DIRECTORY) && !mkdir(STORAGE_DIRECTORY, 0750, true) && !is_dir(STORAGE_DIRECTORY)) {
        return false;
    }

    $record = [
        'username' => $username,
        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        'created_at' => gmdate('c'),
    ];

    $temporaryFile = ADMIN_RECORD_FILE . '.tmp-' . bin2hex(random_bytes(6));
    $written = file_put_contents(
        $temporaryFile,
        json_encode($record, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
        LOCK_EX
    );

    if ($written === false) {
        return false;
    }

    @chmod($temporaryFile, 0640);
    if (!rename($temporaryFile, ADMIN_RECORD_FILE)) {
        @unlink($temporaryFile);
        return false;
    }

    return true;
}

function admin_is_logged_in(): bool
{
    start_admin_session();
    return !empty($_SESSION['admin_authenticated']) && !empty($_SESSION['admin_username']);
}

function require_admin(): void
{
    if (!admin_is_configured()) {
        redirect_to('admin/setup.php');
    }

    if (!admin_is_logged_in()) {
        flash_message('error', 'Please sign in to manage the gallery.');
        redirect_to('admin/login.php');
    }
}

function flash_message(string $type, string $message): void
{
    start_admin_session();
    $_SESSION['flash_messages'][] = ['type' => $type, 'message' => $message];
}

function consume_flash_messages(): array
{
    start_admin_session();
    $messages = $_SESSION['flash_messages'] ?? [];
    unset($_SESSION['flash_messages']);
    return is_array($messages) ? $messages : [];
}

function login_is_rate_limited(): bool
{
    start_admin_session();
    $cutoff = time() - 900;
    $attempts = array_values(array_filter(
        $_SESSION['login_attempts'] ?? [],
        static fn ($timestamp): bool => is_int($timestamp) && $timestamp >= $cutoff
    ));
    $_SESSION['login_attempts'] = $attempts;
    return count($attempts) >= 5;
}

function record_failed_login(): void
{
    start_admin_session();
    $_SESSION['login_attempts'][] = time();
}

function clear_failed_logins(): void
{
    start_admin_session();
    unset($_SESSION['login_attempts']);
}

send_security_headers();

