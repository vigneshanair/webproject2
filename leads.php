<?php
$page_title = 'Lead Board';
require_once __DIR__ . '/includes/config.php';

$case = get_active_case();
$evidence = $_SESSION['evidence_bag'];
$current_priority = get_priority_clues();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected = $_POST['priority'] ?? [];
    // $selected is an array of clue strings
    set_priority_clues($selected);
    $current_priority = get_priority_clues();
    $message = 'Lead board updated. Your most important clues are highlighted.';
}

include __DIR__ . '/includes/header.php';
?>
<h1>Lead Board</h1>
<p class="tagline">
    Choose which clues are your key leads. This helps you focus when reconstructing the crime.
</p>

<section class="card-grid">
    <article class="card form-card">
        <div class="card-title">Select Important Leads</div>
        <?php if (empty($evidence)): ?>
            <p class="muted" style="font-size:0.85rem;">
                You haven’t collected any evidence yet. Visit the Crime Scene, Forensics, or Interrogation Room first.
            </p>
        <?php else: ?>
            <form method="post">
                <div class="form-group">
                    <p class="muted" style="font-size:0.8rem; margin-bottom:0.4rem;">
                        Check the clues you consider most important for this case.
                    </p>
                    <?php foreach ($evidence as $clue): ?>
                        <label style="display:block; font-size:0.85rem; margin-bottom:0.3rem;">
                            <input
                                type="checkbox"
                                name="priority[]"
                                value="<?php echo htmlspecialchars($clue); ?>"
                                <?php if (in_array($clue, $current_priority, true)) echo 'checked'; ?>
                            >
                            <?php echo htmlspecialchars($clue); ?>
                        </label>
                    <?php endforeach; ?>
                </div>
                <button type="submit" class="btn">Save Lead Selection</button>
            </form>
            <?php if ($message): ?>
                <p style="font-size:0.85rem; margin-top:0.7rem;"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>
        <?php endif; ?>
    </article>

    <article class="card">
        <div class="card-title">Important Leads</div>
        <?php if (empty($current_priority)): ?>
            <p class="muted" style="font-size:0.85rem;">
                No leads selected yet. Choose some from the left side.
            </p>
        <?php else: ?>
            <ul class="evidence-list">
                <?php foreach ($current_priority as $clue): ?>
                    <li><?php echo htmlspecialchars($clue); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </article>

    <aside class="card">
        <div class="card-title">Case Context</div>
        <?php if ($case): ?>
            <p style="font-size:0.85rem;">
                <strong><?php echo htmlspecialchars($case['title']); ?></strong><br>
                <?php echo htmlspecialchars($case['objective']); ?>
            </p>
        <?php else: ?>
            <p class="muted" style="font-size:0.85rem;">
                No active case. Choose one from the Cases page.
            </p>
        <?php endif; ?>
    </aside>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
