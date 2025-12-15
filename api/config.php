<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) {
  session_set_cookie_params(['lifetime'=>0,'path'=>'/','httponly'=>true,'samesite'=>'Lax']);
  session_start();
}
header('Content-Type: application/json; charset=utf-8');

const DB_HOST='localhost';
const DB_NAME='YOUR_DB_NAME';
const DB_USER='YOUR_DB_USER';
const DB_PASS='YOUR_DB_PASSWORD';

function pdo(): PDO {
  static $pdo=null;
  if($pdo) return $pdo;
  $dsn='mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4';
  $pdo=new PDO($dsn, DB_USER, DB_PASS, [
    PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES=>false,
  ]);
  return $pdo;
}
function json_ok(array $extra=[]): void { echo json_encode(array_merge(['ok'=>true],$extra),JSON_UNESCAPED_SLASHES); exit; }
function json_err(string $msg,int $code=400): void { http_response_code($code); echo json_encode(['ok'=>false,'error'=>$msg],JSON_UNESCAPED_SLASHES); exit; }
function read_json(): array { $raw=file_get_contents('php://input'); if(!$raw) return []; $d=json_decode($raw,true); return is_array($d)?$d:[]; }
function make_csrf(): string { $t=bin2hex(random_bytes(16)); $_SESSION['csrf']=$t; return $t; }
function ensure_csrf(): void {
  $m=$_SERVER['REQUEST_METHOD'] ?? 'GET'; if($m==='GET') return;
  $tok=$_SERVER['HTTP_X_CSRF_TOKEN'] ?? ''; $sess=$_SESSION['csrf'] ?? '';
  if(!$sess || !$tok || !hash_equals($sess,$tok)) json_err('Bad CSRF token',403);
}
function require_login(): int { if(!isset($_SESSION['uid'])) json_err('Login required',401); return (int)$_SESSION['uid']; }
