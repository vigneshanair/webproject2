<?php
declare(strict_types=1);
require_once __DIR__.'/config.php';
$pdo=pdo();
$method=$_SERVER['REQUEST_METHOD'] ?? 'GET';

if($method==='GET'){
  $user=null;
  if(isset($_SESSION['uid'])){
    $st=$pdo->prepare('SELECT id,username,email,created_at FROM users WHERE id=?');
    $st->execute([$_SESSION['uid']]);
    $user=$st->fetch() ?: null;
  }
  json_ok(['user'=>$user,'csrf'=>($_SESSION['csrf'] ?? null)]);
}

ensure_csrf();
$d=read_json();
$action=$d['action'] ?? '';

if($action==='logout'){
  session_destroy();
  json_ok(['user'=>null,'csrf'=>null]);
}

if($action==='register'){
  $username=trim((string)($d['username'] ?? ''));
  $email=trim((string)($d['email'] ?? ''));
  $password=(string)($d['password'] ?? '');
  if(strlen($username)<3||strlen($username)>24) json_err('Username must be 3–24 chars');
  if(!filter_var($email,FILTER_VALIDATE_EMAIL)) json_err('Invalid email');
  if(strlen($password)<8) json_err('Password must be at least 8 chars');

  $st=$pdo->prepare('SELECT id FROM users WHERE username=? OR email=?');
  $st->execute([$username,$email]);
  if($st->fetch()) json_err('Username or email already in use');

  $hash=password_hash($password,PASSWORD_DEFAULT);

  $pdo->beginTransaction();
  try{
    $st=$pdo->prepare('INSERT INTO users(username,email,password_hash) VALUES(?,?,?)');
    $st->execute([$username,$email,$hash]);
    $uid=(int)$pdo->lastInsertId();
    $pdo->prepare('INSERT INTO profiles(user_id,preferred_theme) VALUES(?,?)')->execute([$uid,'auto']);
    $pdo->prepare('INSERT INTO user_skill(user_id,skill_rating,difficulty_level) VALUES(?,?,?)')->execute([$uid,0.0,1]);
    $pdo->commit();
  }catch(Throwable $e){
    $pdo->rollBack();
    json_err('Registration failed',500);
  }
  $_SESSION['uid']=$uid;
  $csrf=make_csrf();
  json_ok(['user'=>['id'=>$uid,'username'=>$username,'email'=>$email],'csrf'=>$csrf]);
}

if($action==='login'){
  $identifier=trim((string)($d['identifier'] ?? ''));
  $password=(string)($d['password'] ?? '');
  if(!$identifier||!$password) json_err('Missing fields');
  $st=$pdo->prepare('SELECT id,username,email,password_hash FROM users WHERE username=? OR email=?');
  $st->execute([$identifier,$identifier]);
  $u=$st->fetch();
  if(!$u || !password_verify($password,$u['password_hash'])) json_err('Invalid credentials',401);
  $_SESSION['uid']=(int)$u['id'];
  $csrf=make_csrf();
  json_ok(['user'=>['id'=>(int)$u['id'],'username'=>$u['username'],'email'=>$u['email']],'csrf'=>$csrf]);
}

json_err('Unknown action');
