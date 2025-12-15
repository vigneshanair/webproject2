<?php
declare(strict_types=1);
require_once __DIR__.'/config.php';
ensure_csrf();
$pdo=pdo();
$d=read_json();

$sessionId=(int)($d['session_id'] ?? 0);
if($sessionId<=0) json_err('Missing session_id');

$elapsed=(int)($d['elapsed_s'] ?? 0);
$moves=(int)($d['moves'] ?? 0);
if($elapsed<0||$moves<0) json_err('Bad stats');

$uid=isset($_SESSION['uid'])?(int)$_SESSION['uid']:null;

$st=$pdo->prepare('SELECT id,user_id,puzzle_id,size_n,difficulty_rating,status FROM game_sessions WHERE id=?');
$st->execute([$sessionId]);
$sess=$st->fetch();
if(!$sess) json_err('Invalid session',404);
if($sess['status']!=='active') json_err('Session not active',409);
if($uid && $sess['user_id'] && (int)$sess['user_id']!==$uid) json_err('Forbidden',403);

$ach=[];
$pdo->beginTransaction();
try{
  $pdo->prepare('UPDATE game_sessions SET status=?, finished_at=NOW(), elapsed_s=?, moves=? WHERE id=?')
      ->execute(['completed',$elapsed,$moves,$sessionId]);

  $pdo->prepare('INSERT INTO analytics_events(user_id,session_id,event_type,size_n,difficulty_rating,moves,elapsed_s,created_at) VALUES(?,?,?,?,?,?,?,NOW())')
      ->execute([$uid,$sessionId,'completed',(int)$sess['size_n'],(int)$sess['difficulty_rating'],$moves,$elapsed]);

  if($uid){
    $pdo->prepare('INSERT INTO leaderboard(user_id,size_n,best_time_s,best_moves,best_difficulty,updated_at)
                   VALUES(?,?,?,?,?,NOW())
                   ON DUPLICATE KEY UPDATE
                     best_time_s=LEAST(best_time_s,VALUES(best_time_s)),
                     best_moves=LEAST(best_moves,VALUES(best_moves)),
                     best_difficulty=GREATEST(best_difficulty,VALUES(best_difficulty)),
                     updated_at=NOW()')
        ->execute([$uid,(int)$sess['size_n'],$elapsed,$moves,(int)$sess['difficulty_rating']]);

    $n=(int)$sess['size_n']; $diff=(int)$sess['difficulty_rating'];
    $expected=max(10,(int)round(($n*$n*2)+($diff-1)*($n*3)));
    $speedScore=($expected-$elapsed)/max(1,$expected);
    $moveTarget=max(20,$n*$n*2 + ($diff-1)*$n);
    $moveScore=($moveTarget-$moves)/max(1,$moveTarget);
    $delta=0.6*$speedScore+0.4*$moveScore;
    $delta=max(-0.35,min(0.35,$delta));

    $st=$pdo->prepare('SELECT skill_rating,difficulty_level,games_played FROM user_skill WHERE user_id=? FOR UPDATE');
    $st->execute([$uid]);
    $sk=$st->fetch();
    $rating=$sk?(float)$sk['skill_rating']:0.0;
    $gp=$sk?(int)$sk['games_played']:0;

    $rating += $delta;
    $lvl=(int)round(1 + max(-2.0,min(2.0,$rating))*2.0);
    $lvl=max(1,min(10,$lvl + (int)floor($gp/6)));

    $pdo->prepare('INSERT INTO user_skill(user_id,skill_rating,difficulty_level,games_played,last_played_at)
                   VALUES(?,?,?,1,NOW())
                   ON DUPLICATE KEY UPDATE skill_rating=VALUES(skill_rating),difficulty_level=VALUES(difficulty_level),games_played=games_played+1,last_played_at=NOW()')
        ->execute([$uid,$rating,$lvl]);

    $achDefs=[
      ['FAST_30','Speedy Elf', ($elapsed<=30 && $n<=4)],
      ['FAST_60','Workshop Sprinter', ($elapsed<=60 && $n<=4)],
      ['FEW_MOVES','Precision Crafter', ($moves <= $moveTarget)],
      ['BIG_BOARD','Giant Crate Mover', ($n>=8)],
      ['HARD_MODE','Santa Approved', ($diff>=6)],
    ];
    foreach($achDefs as [$code,$name,$cond]){
      if(!$cond) continue;
      $pdo->prepare('INSERT IGNORE INTO achievements(code,name) VALUES(?,?)')->execute([$code,$name]);
      $st2=$pdo->prepare('INSERT IGNORE INTO user_achievements(user_id,achievement_code,unlocked_at) VALUES(?,?,NOW())');
      $st2->execute([$uid,$code]);
      if($st2->rowCount()>0) $ach[]=$name;
    }
  }

  $pdo->commit();
}catch(Throwable $e){
  $pdo->rollBack();
  json_err('Failed to finish session',500);
}

json_ok(['achievements'=>$ach]);
