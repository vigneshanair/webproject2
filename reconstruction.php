<?php
$page_title = 'Reconstruction';
require_once __DIR__ . '/includes/config.php';
$case = get_active_case();
if (!$case) {
    header('Location: cases.php');
    exit;
}

$result  = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $culprit = $_POST['culprit'] ?? '';
    $method  = $_POST['method']  ?? '';
    $time    = $_POST['time']    ?? '';

    if ($culprit === $case['solution']['culprit']
        && $method === $case['solution']['method']
        && $time   === $case['solution']['time']) {

        $success = true;
        $_SESSION['score'] += 15;
        mark_case_solved($case['id']);
        $result = 'Case solved – your reconstruction matches the truth. Leaderboard updated.';
    } else {
        register_mistake(5);
        $result = 'Something does not line up. Review your evidence and try again.';
    }
}

include __DIR__ . '/includes/header.php';
?>
<h1>Crime Reconstruction</h1>
<p class="tagline">Use all your gathered clues to lock in the culprit, method, and time.</p>

<section class="card-grid">
    <article class="card form-card">
        <div class="card-title">Build Your Theory</div>
        <form method="post">
            <div class="form-group">
                <label for="culprit">Culprit</label>
                <select name="culprit" id="culprit">
                    <?php foreach ($case['suspects'] as $name => $p): ?>
                        <option value="<?php echo htmlspecialchars($name); ?>">
                            <?php echo htmlspecialchars($name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="method">Entry Method</label>
                <select name="method" id="method">
                    <?php foreach ($case['possible_methods'] as $m): ?>
                        <option value="<?php echo htmlspecialchars($m); ?>">
                            <?php echo htmlspecialchars($m); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="time">Time of Heist</label>
                <select name="time" id="time">
                    <?php foreach ($case['possible_times'] as $t): ?>
                        <option value="<?php echo htmlspecialchars($t); ?>">
                            <?php echo htmlspecialchars($t); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn">Submit Reconstruction</button>
        </form>

        <?php if ($result): ?>
            <p class="info-message"><?php echo htmlspecialchars($result); ?></p>
            <p class="stats">Score: <strong><?php echo (int)$_SESSION['score']; ?></strong></p>
        <?php endif; ?>
    </article>

    <article class="card">
        <div class="card-title">Reconstruction Hints</div>
        <ul class="simple-list">
            <li>Does your culprit have both motive and access?</li>
            <li>Does your entry method match footprints and security logs?</li>
            <li>Does the chosen time align with alibis and blackout windows?</li>
        </ul>
    </article>

    <aside class="card">
        <div class="card-title">Evidence Bag</div>
        <div class="evidence-panel">
            <?php if (empty($_SESSION['evidence_bag'])): ?>
                <p class="muted">Your theory will be weak without gathered evidence.</p>
            <?php else: ?>
                <ul class="evidence-list">
                    <?php foreach ($_SESSION['evidence_bag'] as $clue): ?>
                        <li><?php echo htmlspecialchars($clue); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </aside>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
