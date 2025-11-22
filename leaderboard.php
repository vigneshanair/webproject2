<?php
$page_title = 'Leaderboard – Cryptic Quest';
require_once __DIR__ . '/includes/config.php';
include __DIR__ . '/includes/header.php';
?>

<h1>Leaderboard</h1>
<p class="tagline">
    Simple placeholder leaderboard for this session. In a full version this would read from a database.
</p>

<section class="card-grid">
    <article class="card">
        <div class="card-title">Your Session</div>
        <div class="card-body">
            <p style="font-size:0.86rem;">
                Detective: <strong><?php echo htmlspecialchars($_SESSION['player_name']); ?></strong><br>
                Score: <strong><?php echo (int) $_SESSION['score']; ?></strong><br>
                Difficulty: <strong><?php echo (int) $_SESSION['difficulty']; ?></strong>
            </p>
        </div>
    </article>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
