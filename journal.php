<?php
$page_title = 'Detective Journal';
require_once __DIR__ . '/includes/config.php';

$case = get_active_case();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $note = $_POST['note'] ?? '';
    save_journal_entry($note);
    $message = 'Journal entry saved.';
}

$entries = read_journal_entries_for_player();

include __DIR__ . '/includes/header.php';
?>
<h1>Detective Journal</h1>
<p class="tagline">
    Use your journal to track theories, suspect motives, and connections between cases.
</p>

<section class="card-grid">
    <article class="card form-card">
        <div class="card-title">New Journal Entry</div>
        <form method="post">
            <div class="form-group">
                <label for="note">Your notes</label>
                <textarea id="note" name="note" placeholder="Write your thoughts about the case..."></textarea>
            </div>
            <button type="submit" class="btn">Save Entry</button>
        </form>
        <?php if ($message): ?>
            <p style="font-size:0.85rem; margin-top:0.7rem;"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <?php if ($case): ?>
            <p class="muted" style="font-size:0.8rem; margin-top:0.6rem;">
                Active case: <strong><?php echo htmlspecialchars($case['title']); ?></strong>
            </p>
        <?php else: ?>
            <p class="muted" style="font-size:0.8rem; margin-top:0.6rem;">
                No active case selected.
            </p>
        <?php endif; ?>
    </article>

    <article class="card">
        <div class="card-title">Recent Entries</div>
        <?php if (empty($entries)): ?>
            <p class="muted" style="font-size:0.85rem;">
                You haven’t written any journal entries yet.
            </p>
        <?php else: ?>
            <ul style="list-style:none; padding-left:0; font-size:0.85rem;">
                <?php foreach (array_reverse($entries) as $entry): ?>
                    <li style="margin-bottom:0.6rem;">
                        <strong><?php echo htmlspecialchars($entry['time']); ?></strong>
                        <?php if ($entry['case']): ?>
                            – <em><?php echo htmlspecialchars($entry['case']); ?></em>
                        <?php endif; ?>
                        <br>
                        <?php echo nl2br(htmlspecialchars($entry['text'])); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </article>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
