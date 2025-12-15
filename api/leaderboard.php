<?php
declare(strict_types=1);
require_once __DIR__.'/config.php';
$pdo=pdo();

$size=isset($_GET['size'])?(int)$_GET['size']:4;
$mode=isset($_GET['mode'])?(string)$_GET['mode']:'time';
if($size<3||$size>10) $size=4;
if(!in_array($mode,['time','moves'],true)) $mode='time';
$order=$mode==='moves'?'best_moves ASC':'best_time_s ASC';

$st=$pdo->prepare("SELECT u.username, l.best_time_s, l.best_moves, l.best_difficulty
                   FROM leaderboard l JOIN users u ON u.id=l.user_id
                   WHERE l.size_n=?
                   ORDER BY $order
                   LIMIT 20");
$st->execute([$size]);
json_ok(['rows'=>$st->fetchAll()]);
