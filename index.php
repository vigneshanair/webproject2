<?php
// index.php
require_once 'game_state.php';

// Handle login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['detective_name'] ?? '');
    $badge = trim($_POST['badge_title'] ?? '');

    if ($name !== '') {
        $_SESSION['detective']['name']  = $name;
        $_SESSION['detective']['badge'] = $badge;
        header('Location: cases.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cryptic Quest – Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="login-body">
    <div class="login-overlay"></div>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-left">
                <h1 class="game-logo">Cryptic Quest</h1>
                <p class="tagline">Metro Crime Analysis Unit – Secure Access</p>
                <p class="login-description">
                    Step into the role of a crime scene investigator. Take on interconnected cases, 
                    track suspects, and build your legend.
                </p>
            </div>
            <div class="login-right">
                <h2>Detective Login</h2>
                <form method="post">
                    <label for="detective_name">Detective Name</label>
                    <input type="text" id="detective_name" name="detective_name" required>

                    <label for="badge_title">Badge Title</label>
                    <select id="badge_title" name="badge_title">
                        <option value="Rookie Detective">Rookie Detective</option>
                        <option value="Senior Investigator">Senior Investigator</option>
                        <option value="Chief Inspector">Chief Inspector</option>
                    </select>

                    <button type="submit" class="btn-primary">Begin Investigation</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
