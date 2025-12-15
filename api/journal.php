<?php
declare(strict_types=1);
require_once __DIR__.'/config.php';
$pdo=pdo();
$method=$_SERVER['REQUEST_METHOD'] ?? 'GET';

if($method==='GET'){
  if(!isset($_SESSION['uid'])) json_ok(['entries'=>[]]);
  $uid=(int)$_SESSION['uid'];
  $st=$pdo->prepare('SELECT entry, DATE_FORMAT(created_at,"%Y-%m-%d %H:%i") as created_at
                     FROM dev_journal WHERE user_id=? ORDER BY created_at DESC LIMIT 30');
  $st->execute([$uid]);
  json_ok(['entries'=>$st->fetchAll()]);
}

ensure_csrf();
$uid=require_login();
$d=read_json();
$entry=trim((string)($d['entry'] ?? ''));
if(!$entry) json_err('Empty entry');
$pdo->prepare('INSERT INTO dev_journal(user_id,entry,created_at) VALUES(?,?,NOW())')->execute([$uid,$entry]);
json_ok(['saved'=>true]);
