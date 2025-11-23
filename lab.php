<?php
require_once 'game_state.php';
require_detective();

$caseId = (int)($_GET['case'] ?? 1);
$cases  = get_cases();
if (!isset($cases[$caseId])) {
    header('Location: cases.php');
    exit;
}

$resultMsg = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_notes'])) {
        save_notes_for_case($caseId, $_POST['notes'] ?? '');
    }

    if (isset($_POST['fingerprint_choice'])) {
        if ($_POST['fingerprint_choice'] === 'sample_b') {
            $resultMsg = "Match confirmed: Sample B aligns with the crime scene fingerprint.";
            add_evidence($caseId, 'fingerprint_match', 'Fingerprint from Sample B matches crime scene print.');
            set_case_progress($caseId, max(get_case_progress($caseId), 70));
        } else {
            $resultMsg = "No match. The patterns don’t line up clearly.";
        }
    }

    if (isset($_POST['evidence_relation'])) {
        if ($_POST['evidence_relation'] === 'contradicts') {
            $resultMsg = "Good catch. The receipt time contradicts the suspect’s alibi.";
            add_evidence($caseId, 'receipt_contradiction', 'Receipt time contradicts suspect’s claimed location.');
            set_case_progress($caseId, max(get_case_progress($caseId), 85));
        } else {
            $resultMsg = "Your classification is noted, but keep an eye on inconsistencies.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forensic Lab – <?php echo htmlspecialchars(get_case_title($caseId)); ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php render_header('Forensic Lab'); ?>

<main class="main-layout with-sidebar">
    <section class="forensic-lab">
        <a href="case_dashboard.php?case=<?php echo $caseId; ?>" class="back-button">← Back to Case Dashboard</a>

        <h2>Forensic Analysis Lab</h2>
        <p class="section-intro">
            Time to let the evidence speak. Match prints and compare facts against statements.
        </p>

        <?php if (!empty($resultMsg)): ?>
            <div class="flash-message"><?php echo htmlspecialchars($resultMsg); ?></div>
        <?php endif; ?>

        <div class="lab-grid">
            <div class="lab-module">
                <h3>Fingerprint Matching</h3>
                <p>Compare the crime scene fingerprint with the available samples.</p>
                <div class="lab-visual-placeholder">
                    <img src="fingerprint.png" alt="Crime scene fingerprint" class="lab-fingerprint-image">
                </div>
                <form method="post" class="fingerprint-options">
                    <label class="fingerprint-option">
                        <input type="radio" name="fingerprint_choice" value="sample_a" required>
                        Sample A – Broad arches
                    </label>
                    <label class="fingerprint-option">
                        <input type="radio" name="fingerprint_choice" value="sample_b">
                        Sample B – Tight whorls
                    </label>
                    <label class="fingerprint-option">
                        <input type="radio" name="fingerprint_choice" value="sample_c">
                        Sample C – Loose loops
                    </label>
                    <button type="submit" class="btn-primary">Analyze Match</button>
                </form>
            </div>

            <div class="lab-module">
                <h3>Evidence Comparison</h3>
                <p>Does this new piece of evidence support or contradict the suspect’s story?</p>
                <div class="comparison-cards">
                    <div class="comparison-card">
                        <h4>Suspect Statement</h4>
                        <p>“I left the gala at 9:00 PM exactly. I never went near the study after that.”</p>
                    </div>
                    <div class="comparison-card">
                        <h4>New Evidence</h4>
                        <p>A restaurant receipt with the suspect’s name, issued at 10:15 PM near the gala venue.</p>
                    </div>
                </div>
                <form method="post" class="evidence-relation-form">
                    <p>How does this evidence relate to their story?</p>
                    <label>
                        <input type="radio" name="evidence_relation" value="supports" required>
                        Supports their statement
                    </label>
                    <label>
                        <input type="radio" name="evidence_relation" value="contradicts">
                        Contradicts their statement
                    </label>
                    <label>
                        <input type="radio" name="evidence_relation" value="unrelated">
                        Unrelated / uncertain
                    </label>
                    <button type="submit" class="btn-primary">Record Interpretation</button>
                </form>
            </div>
        </div>
    </section>

    <?php render_evidence_bag_sidebar($caseId); ?>
    <?php render_notebook($caseId); ?>
</main>
</body>
</html>
