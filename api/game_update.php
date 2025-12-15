<?php
declare(strict_types=1);
require_once __DIR__.'/config.php';
ensure_csrf();
$pdo=pdo();
$d=read_json();

$sessionId=(int)($d['session_id'] ?? 0);
if($sessionId<=0) json_err('Missing session_id');

$tiles=$d['tiles'] ?? null;
$moves=array_key_exists('moves',$d)?(int)$d['moves']:null;
$elapsed=array_key_exists('elapsed_s',$d)?(int)$d['elapsed_s']:null;
$hintUsed=!empty($d['hint_used']);

$uid=isset($_SESSION['uid'])?(int)$_SESSION['uid']:null;

$st=$pdo->prepare('SELECT id,user_id,status,magic_left FROM game_sessions WHERE id=?');
$st->execute([$sessionId]);
$sess=$st->fetch();
if(!$sess) json_err('Invalid session',404);
if($sess['status']!=='active') json_err('Session not active',409);
if($uid && $sess['user_id'] && (int)$sess['user_id']!==$uid) json_err('Forbidden',403);

$pdo->beginTransaction();
try{
  if(is_array($tiles)){
    $pdo->prepare('UPDATE game_sessions SET current_state_json=? WHERE id=?')->execute([json_encode($tiles),$sessionId]);
  }
  if($moves!==null){
    $pdo->prepare('UPDATE game_sessions SET moves=? WHERE id=?')->execute([$moves,$sessionId]);
  }
  if($elapsed!==null){
    $pdo->prepare('UPDATE game_sessions SET elapsed_s=? WHERE id=?')->execute([$elapsed,$sessionId]);
  }
  if($hintUsed){
    $ml=(int)$sess['magic_left'];
    if($ml>0){
      $ml--;
      $pdo->prepare('UPDATE game_sessions SET magic_left=? WHERE id=?')->execute([$ml,$sessionId]);
      $pdo->prepare('INSERT INTO session_events(session_id,event_type,created_at) VALUES(?,?,NOW())')->execute([$sessionId,'hint_used']);
    }
  }
  $pdo->commit();
}catch(Throwable $e){
  $pdo->rollBack();
  json_err('Failed to update session',500);
}

json_ok(['updated'=>true]);
