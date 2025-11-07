<?php
session_start();

function current_user() { return $_SESSION['user'] ?? null; }
function require_login() {
  if (!current_user()) { header('Location: login.php'); exit; }
}
function do_login($user){
  $_SESSION['user'] = ['id'=>$user['id'], 'username'=>$user['username']];
  session_regenerate_id(true);
}
function do_logout(){
  $_SESSION = [];
  if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time()-42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
  }
  session_destroy();
}
