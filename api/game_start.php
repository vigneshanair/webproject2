<?php
declare(strict_types=1);
require_once __DIR__.'/config.php';
ensure_csrf();
$pdo=pdo();
$d=read_json();
$size=(int)($d['size'] ?? 4);
if($size<3||$size>10) json_err('Size must be 3..10');

$uid=isset($_SESSION['uid'])?(int)$_SESSION['uid']:null;
$difficulty=1;
if($uid){
  $st=$pdo->prepare('SELECT difficulty_level FROM user_skill WHERE user_id=?');
  $st->execute([$uid]);
  $row=$st->fetch();
  if($row) $difficulty=(int)$row['difficulty_level'];
}
$difficulty=max(1,min(10,$difficulty));

$base=($size*$size)*2;
$steps=(int)round($base + ($difficulty-1)*($size*3));
$steps=max(40,min(1200,$steps));

$magic_left=max(1,6-(int)floor(($difficulty-1)/2));
if($size>=8) $magic_left=max(1,$magic_left-1);

function neighbors(int $blank,int $n): array{
  $r=intdiv($blank,$n); $c=$blank%$n; $out=[];
  if($r>0) $out[]=$blank-$n;
  if($r<$n-1) $out[]=$blank+$n;
  if($c>0) $out[]=$blank-1;
  if($c<$n-1) $out[]=$blank+1;
  return $out;
}

$tiles=[];
for($i=1;$i<$size*$size;$i++) $tiles[]=$i;
$tiles[] = 0;
$blank=$size*$size-1;
$path=[]; $prev=-1;
for($k=0;$k<$steps;$k++){
  $opts=neighbors($blank,$size);
  if($prev!==-1 && count($opts)>1){
    $opts=array_values(array_filter($opts, fn($x)=>$x!==$prev));
  }
  $choice=$opts[random_int(0,count($opts)-1)];
  $tmp=$tiles[$choice]; $tiles[$choice]=0; $tiles[$blank]=$tmp;
  $path[]=$blank;
  $prev=$blank; $blank=$choice;
}

try{
  $pdo->beginTransaction();
  $st=$pdo->prepare('INSERT INTO puzzles(size_n,difficulty_rating,shuffle_steps,initial_state_json,solution_path_json) VALUES(?,?,?,?,?)');
  $st->execute([$size,$difficulty,$steps,json_encode($tiles),json_encode($path)]);
  $puzzleId=(int)$pdo->lastInsertId();

  $st=$pdo->prepare('INSERT INTO game_sessions(user_id,puzzle_id,size_n,difficulty_rating,started_at,status,magic_left) VALUES(?,?,?,?,NOW(),?,?)');
  $st->execute([$uid,$puzzleId,$size,$difficulty,'active',$magic_left]);
  $sessionId=(int)$pdo->lastInsertId();
  $pdo->commit();
}catch(Throwable $e){
  $pdo->rollBack();
  json_err('Failed to start session (DB)',500);
}

json_ok(['session_id'=>$sessionId,'puzzle'=>['tiles'=>$tiles,'blank'=>$blank,'size'=>$size,'difficulty'=>$difficulty,'magic_left'=>$magic_left]]);
