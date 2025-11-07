<?php
function db() {
  static $pdo;
  if ($pdo) return $pdo;

  $dsn = 'mysql:host=127.0.0.1;dbname=blog;charset=utf8mb4';
  $user = 'root';   // XAMPP default
  $pass = '';       // blank password

  $pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
  return $pdo;
}
function h($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }
