<?php
$page_title = 'Cases – Cryptic Quest';
require_once __DIR__ . '/includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action  = $_POST['action'] ?? '';
    $caseId  = $_POST['case_id'] ?? '';

    if ($action === 'priority') {
        $_SESSION['priority_case'] = $caseId;
        $_SESSION['active_case']   = $caseId;
    } elseif ($action === 'open') {
        set_active_case($caseId);
        header('Location: case_dashboard.php');
        exit;
    }
}

include __DIR__ . '/includes/header.php';

$currentLevel = (int)$_SESSION['current_level'];
?>

<section>
    <div class="page-header">
        <h1>Cases</h1>
        <p class="page-subtitle">
            Choose which investigation to focus on. Set a priority case and open it in the dashboard.
        </p>
    </div>

    <form method="post" class="card-grid">
        <?php global $CASES; ?>
        <?php foreach ($CASES as $id => $case): ?>
            <?php
            $isPriority = ($_SESSION['priority_case'] === $id);
            $progress   = get_case_progress($id);
            ?>
            <article class="card">
                <div class="card-title">
                    <?php echo htmlspecialchars($case['title']); ?>
                </div>
                <div class="card-subtitle">
                    Level <?php echo (int)$case['level']; ?> •
                    Location: <?php echo htmlspecialchars($case['location']); ?>
                </div>
                <p style="font-size:0.85rem; color:var(--text-soft); margin-bottom:0.6rem;">
                    <?php echo htmlspecialchars($case['summary']); ?>
                </p>

                <div class="progress-card" style="font-size:0.8rem;">
                    Progress: <strong><?php echo $progress; ?>%</strong>
                    <div class="progress-track">
                        <div class="progress-fill" style="width: <?php echo $progress; ?>%;"></div>
                    </div>
                </div>

                <div class="card-footer">
                    <div>
                        <?php if ($isPriority): ?>
                            <span class="badge badge-easy">Priority Case</span>
                        <?php else: ?>
                            <span class="badge">Available</span>
                        <?php endif; ?>
                    </div>

                    <div style="display:flex; gap:0.4rem;">
                        <input type="hidden" name="case_id" value="<?php echo htmlspecialchars($id); ?>">
                        <button class="btn-secondary" name="action" value="priority" type="submit">
                            Set Priority
                        </button>
                        <button class="btn" name="action" value="open" type="submit">
                            Open
                        </button>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </form>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
