<?php
$page_title = 'Forensics Lab';
require_once __DIR__ . '/includes/config.php';

$case = get_active_case();
if (!$case) {
    header('Location: cases.php');
    exit;
}

$difficulty = $_SESSION['difficulty'];

if ($difficulty === 1) {
    $options = $case['forensics']['options_easy'];
} elseif ($difficulty === 2) {
    $options = $case['forensics']['options_medium'];
} else {
    $options = $case['forensics']['options_hard'];
}

$feedback = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $choice = (int) ($_POST['choice'] ?? -1);
    if ($choice === $case['forensics']['correct_index']) {
        add_evidence($case['forensics']['clue']);
        $feedback = 'Correct sample. This goes straight into your evidence bag.';
    } else {
        register_mistake();
        $feedback = 'That sample doesn’t tie strongly to the case. Try again with a better match.';
    }
}

include __DIR__ . '/includes/header.php';
?>
<div class="screen-header">
    <h1>Forensics Lab Console</h1>
    <p class="tagline">Select the sample that gives you the strongest link between suspect and crime scene.</p>
</div>

<section class="board-layout">
    <article class="panel panel-main">
        <h2>Sample Analysis</h2>
        <p><?php echo htmlspecialchars($case['forensics']['question']); ?></p>
        <form method="post" class="form-card">
            <?php foreach ($options as $index => $opt): ?>
                <div class="form-group">
                    <label>
                        <input type="radio" name="choice" value="<?php echo $index; ?>" required>
                        <?php echo htmlspecialchars($opt); ?>
                    </label>
                </div>
            <?php endforeach; ?>
            <button type="submit" class="btn">Analyze Sample</button>
        </form>

        <?php if ($feedback): ?>
            <p class="feedback"><?php echo htmlspecialchars($feedback); ?></p>
        <?php endif; ?>

        <p class="muted small">
            Current difficulty: <?php echo (int) $_SESSION['difficulty']; ?>  
            (adapts based on your score and mistakes)
        </p>
    </article>

    <aside class="panel panel-side">
        <h2>Evidence Bag</h2>
        <div class="evidence-panel">
            <?php if (empty($_SESSION['evidence_bag'])): ?>
                <p class="muted">No forensic clues yet. A correct sample will be added here.</p>
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
