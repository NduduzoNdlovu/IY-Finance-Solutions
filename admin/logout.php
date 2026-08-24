<?php
declare(strict_types=1);

require dirname(__DIR__) . '/config/bootstrap.php';
start_admin_session();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_is_valid($_POST['csrf_token'] ?? null)) {
    http_response_code(405);
    exit('Method not allowed.');
}

$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $parameters = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $parameters['path'], $parameters['domain'], $parameters['secure'], $parameters['httponly']);
}
session_destroy();
redirect_to('admin/login.php');

