<?php
// verdict.php
require_once 'game_state.php';
require_detective();

$caseId = (int)($_GET['case'] ?? 1);
$cases  = get_cases();
if (!isset($cases[$caseId])) {
    header('Location: cases.php'); exit;
}

$suspects = get_suspects_for_case($caseId);
$evidence = get_evidence_for_case($caseId);
$verdictMsg = null;

$correctCulpritId = 1; // Lena for case 1

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_notes'])) {
        save_notes_for_case($caseId, $_POST['notes'] ?? '');
    }

    if (isset($_POST['culprit_id'])) {
        $chosen = (int)$_POST['culprit_id'];
        $motive = trim($_POST['motive'] ?? '');

        if ($chosen === $correctCulpritId) {
            $verdictMsg = "CASE CLOSED – Your report nails the culprit with solid evidence.";
            set_case_progress($caseId, 100);
        } else {
            $verdictMsg = "CASE UNSOLVED – Your accusation doesn’t align with critical evidence. The truth remains hidden.";
            set_case_progress($caseId, max(get_case_progress($caseId), 90));
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Final Verdict – <?php echo htmlspecialchars(get_case_title($caseId)); ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php render_header('Final Verdict'); ?>

<main class="main-layout with-sidebar">
    <section class="verdict-page">
        <a href="case_dashboard.php?case=<?php echo $caseId; ?>" class="back-link">
            ← Back to Case Dashboard
        </a>

        <h2>Submit Final Case Report</h2>
        <p class="section-intro">
            Review your suspects and evidence, then submit your official verdict to the department.
        </p>

        <?php if (!empty($verdictMsg)): ?>
            <div class="verdict-message <?php echo (get_case_progress($caseId) === 100) ? 'case-closed' : 'case-unsolved'; ?>">
                <?php echo htmlspecialchars($verdictMsg); ?>
            </div>
        <?php endif; ?>

        <div class="verdict-layout">
            <div class="verdict-report">
                <h3>Case Summary</h3>
                <p><strong>Case:</strong> <?php echo htmlspecialchars(get_case_title($caseId)); ?></p>

                <h4>Collected Evidence</h4>
                <ul class="verdict-evidence-list">
                    <?php if (empty($evidence)): ?>
                        <li>No evidence logged. This report will be very weak.</li>
                    <?php else: ?>
                        <?php foreach ($evidence as $label): ?>
                            <li><?php echo htmlspecialchars($label); ?></li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>

                <form method="post" class="verdict-form">
                    <h4>Primary Suspect</h4>
                    <select name="culprit_id" required>
                        <option value="">Select a suspect</option>
                        <?php foreach ($suspects as $id => $s): ?>
                            <option value="<?php echo $id; ?>">
                                <?php echo htmlspecialchars($s['name'] . ' – ' . $s['role']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <h4>Motive (your summary)</h4>
                    <textarea name="motive" rows="4"
                              placeholder="Summarize the motive based on what you’ve uncovered."></textarea>

                    <button type="submit" class="btn-primary">Submit Final Report</button>
                </form>

                <div class="verdict-actions">
                    <a href="cases.php" class="btn-secondary">Back to Case Board</a>
                    <a href="leaderboard.php" class="btn-secondary">View Leaderboard</a>
                </div>
            </div>
        </div>
    </section>

    <?php render_evidence_bag_sidebar($caseId); ?>
    <?php render_notebook($caseId); ?>
</main>
</body>
</html>
