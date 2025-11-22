<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($page_title ?? 'Cryptic Quest CSI'); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Your main HUD theme CSS -->
    <link rel="stylesheet" href="game.css?v=1">
</head>
<body class="app-wrapper">
    <header class="top-bar">
        <div class="brand">
            <div class="brand-mark"><span>CQ</span></div>
            <div class="brand-text">
                <strong>Cryptic Quest</strong> CSI
            </div>
        </div>

        <nav class="nav-links">
            <a href="index.php">Home</a>
            <a href="levels.php">Levels</a>
            <a href="cases.php">Cases</a>
            <a href="case_dashboard.php">Case Dashboard</a>
            <a href="interrogations.php">Interrogations</a>
        </nav>

        <div class="player-badge">
            Detective: <?php echo htmlspecialchars($_SESSION['player_name'] ?: 'Unassigned'); ?>
        </div>
    </header>

    <main class="content">
