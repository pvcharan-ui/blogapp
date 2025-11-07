<?php
// --- Secure sessions ---
function start_secure_session() {
  if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
      'httponly' => true,
      'samesite' => 'Lax',
      'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    ]);
    session_start();
  }
}
start_secure_session();

// --- Current user + roles ---
function current_user() { return $_SESSION['user'] ?? null; }
function is_admin() { return (current_user()['role'] ?? 'user') === 'admin'; }

function require_login() {
  if (!current_user()) { header('Location: login.php'); exit; }
}
function require_admin() {
  if (!is_admin()) { http_response_code(403); exit('Forbidden'); }
}

// --- Login / logout ---
function do_login(array $user) {
  $_SESSION['user'] = [
    'id' => $user['id'],
    'username' => $user['username'],
    'role' => $user['role'] ?? 'user'
  ];
  session_regenerate_id(true);
}
function do_logout() {
  $_SESSION = [];
  if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time()-42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
  }
  session_destroy();
}

// --- CSRF protection ---
function csrf_token() {
  if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
  }
  return $_SESSION['csrf'];
}
function csrf_field() {
  echo '<input type="hidden" name="_token" value="'.htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8').'">';
}
function verify_csrf_post() {
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $t = $_POST['_token'] ?? '';
    if (!hash_equals($_SESSION['csrf'] ?? '', $t)) {
      http_response_code(419); exit('CSRF token mismatch');
    }
  }
}
