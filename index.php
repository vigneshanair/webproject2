<?php
$page_title = 'Welcome – Cryptic Quest CSI';
require_once __DIR__ . '/includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['player_name'] ?? '');
    if ($name !== '') {
        $_SESSION['player_name'] = $name;
        header('Location: levels.php');
        exit;
    }
}

include __DIR__ . '/includes/header.php';
?>

<section class="card-grid">
    <article class="card" style="grid-column: span 2;">
        <div class="page-header">
            <h1>Welcome, Detective</h1>
            <p class="page-subtitle">
                Step into a clean new investigation. Choose your codename to begin.
            </p>
        </div>

        <form method="post" class="form-card">
            <div class="form-group">
                <label for="player_name">Detective Name</label>
                <input type="text"
                       id="player_name"
                       name="player_name"
                       value="<?php echo htmlspecialchars($_SESSION['player_name']); ?>"
                       placeholder="Enter your detective name">
            </div>
            <button type="submit" class="btn">Assign Detective</button>
        </form>
    </article>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
