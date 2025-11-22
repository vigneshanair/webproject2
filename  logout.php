<?php
require_once __DIR__ . '/includes/config.php';

$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'], $params['secure'], $params['httponly']
    );
}
session_destroy();

session_start();
$_SESSION['initialized']  = true;
$_SESSION['player_name']  = '';
$_SESSION['active_case']  = null;
$_SESSION['evidence_bag'] = [];
$_SESSION['score']        = 0;
$_SESSION['difficulty']   = 1;
$_SESSION['mistakes']     = 0;
$_SESSION['cases_solved'] = [];
$_SESSION['notebook']     = [];

header('Location: index.php');
exit;
