<?php
$page_title = 'Detective Notebook';
require_once __DIR__ . '/includes/config.php';

$case = get_active_case();

// key for notebook based on active case + section
$current_key = '';
if ($case) {
    $current_key = $case['id'] . '-general';
}

$saved_text = $current_key ? get_notebook_entry($current_key) : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $current_key) {
    $text = trim($_POST['notes'] ?? '');
    add_notebook_entry($current_key, $text);
    $saved_text = $text;
}

include __DIR__ . '/includes/header.php';
?>
<h1>Detective Notebook</h1>
<p class="tagline">
    Jot down your own theories, suspect links, and timeline notes for the current case.
</p>

<section class="card-grid">
    <article class="card form-card" style="grid-column: span 2;">
        <div class="card-title">
            <?php if ($case): ?>
                Notes for: <?php echo htmlspecialchars($case['title']); ?>
            <?php else: ?>
                No active case selected
            <?php endif; ?>
        </div>

        <?php if ($case): ?>
            <form method="post">
                <div class="form-group">
                    <label for="notes">Your investigation notes</label>
                    <textarea id="notes" name="notes"
                              placeholder="Write your observations, suspect motives, contradictions, and timeline here."><?php
                        echo htmlspecialchars($saved_text);
                    ?></textarea>
                </div>
                <button type="submit" class="btn">Save Notebook Entry</button>
            </form>
        <?php else: ?>
            <p class="muted">Start or continue a case first so the notebook can be attached to it.</p>
        <?php endif; ?>
    </article>

    <aside class="card">
        <div class="card-title">Tips</div>
        <p class="muted" style="font-size:0.85rem;">
            Use the notebook to track:
        </p>
        <ul style="font-size:0.85rem; padding-left:1.1rem;">
            <li>Contradictions in suspect statements</li>
            <li>Which clue points to which suspect</li>
            <li>Your current theory before Reconstruction</li>
        </ul>
    </aside>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
