<?php
$page_title = 'Case Dashboard – Cryptic Quest';
require_once __DIR__ . '/includes/config.php';

$caseId = get_active_case_id();
$case   = get_active_case();

if (!$case) {
    $case = [
        'title'    => 'No Active Case',
        'location' => '—',
        'summary'  => 'Choose a case from the Cases screen.',
        'suspect'  => '—'
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['advance']) && $caseId) {
        $current = get_case_progress($caseId);
        $next    = min(100, $current + 20);
        set_case_progress($caseId, $next);

        if ($next >= 100) {
            unlock_next_level((int)$_SESSION['current_level']);
        }

        header('Location: case_dashboard.php');
        exit;
    }
}

include __DIR__ . '/includes/header.php';

$progress = get_case_progress($caseId);
?>

<section class="card-grid">
    <article class="card" style="grid-column: span 2;">
        <div class="page-header">
            <h1><?php echo htmlspecialchars($case['title']); ?></h1>
            <p class="page-subtitle">
                Level <?php echo (int)$case['level']; ?> •
                Location: <?php echo htmlspecialchars($case['location']); ?>
            </p>
        </div>

        <p style="font-size:0.9rem; color:var(--text-soft); margin-bottom:0.9rem;">
            <?php echo htmlspecialchars($case['summary']); ?>
        </p>

        <ul style="font-size:0.85rem; color:var(--text-soft); padding-left:1.1rem; margin-bottom:1rem;">
            <li>Collect key evidence and add it to the evidence bag.</li>
            <li>Run at least one forensic check (fingerprints or patterns).</li>
            <li>Interrogate the main suspect: <?php echo htmlspecialchars($case['suspect']); ?>.</li>
            <li>Reconstruct the timeline and lock in your final theory.</li>
        </ul>

        <form method="post">
            <button type="submit" name="advance" class="btn">
                Advance Investigation
            </button>
        </form>
    </article>

    <aside class="card progress-card">
        <div class="card-title">Investigation Status</div>
        <p style="font-size:0.85rem; color:var(--text-soft); margin-bottom:0.4rem;">
            Overall completion of this case.
        </p>

        <div style="font-size:0.85rem;">
            Progress: <strong><?php echo $progress; ?>%</strong>
            <div class="progress-track" style="margin-top:0.35rem;">
                <div class="progress-fill" style="width: <?php echo $progress; ?>%;"></div>
            </div>
        </div>

        <p style="font-size:0.8rem; color:var(--text-muted); margin-top:0.7rem;">
            Finish this case to help unlock higher difficulty levels.
        </p>
    </aside>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
