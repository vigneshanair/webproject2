<?php
require_once 'game_state.php';
require_detective();

$caseId = (int)($_GET['case'] ?? 1);
$cases  = get_cases();
if (!isset($cases[$caseId])) {
    header('Location: cases.php');
    exit;
}

$message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_notes'])) {
        save_notes_for_case($caseId, $_POST['notes'] ?? '');
    }
    if (isset($_POST['search_zone'])) {
        $zone = $_POST['search_zone'];
        switch ($zone) {
            case 'desk':
                add_evidence($caseId, 'gala_invite', 'Gala invitation with suspicious time stamp');
                set_case_progress($caseId, max(get_case_progress($caseId), 20));
                $message = "You find a gala invitation with a time that doesn’t match the suspect’s story.";
                break;
            case 'window':
                add_evidence($caseId, 'footprints', 'Smudged footprints near the open window');
                set_case_progress($caseId, max(get_case_progress($caseId), 35));
                $message = "Outside the window, faint footprints suggest a hurried escape.";
                break;
            case 'floor':
                add_evidence($caseId, 'bracelet_clasp', 'Broken bracelet clasp under the table');
                set_case_progress($caseId, max(get_case_progress($caseId), 50));
                $message = "Under the table, you spot a broken bracelet clasp that matches the missing item.";
                break;
            default:
                $message = "You look around, but find nothing new.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Crime Scene – <?php echo htmlspecialchars(get_case_title($caseId)); ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php render_header('Crime Scene'); ?>

<main class="main-layout with-sidebar">
    <section class="crime-scene">
        <a href="case_dashboard.php?case=<?php echo $caseId; ?>" class="back-button">← Back to Case Dashboard</a>

        <h2>CRIME SCENE: THE VANISHING BRACELET</h2>
        <p class="section-intro">
            The room is quiet, but it’s screaming with clues. Choose an area to search carefully.
        </p>

        <?php if (!empty($message)): ?>
            <div class="flash-message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <div class="crime-scene-layout">
            <div class="scene-visual">
                <p class="scene-title">Study crime scene for the Vanishing Bracelet case</p>

                <div class="scene-image-wrapper" id="sceneWrapper">
                    <img
                        src="vanishing_bracelet_scene.png"
                        alt="Crime scene study room"
                        class="scene-image"
                        id="crimeSceneImage"
                    >
                    <div class="magnifier-lens" id="magnifierLens"></div>
                </div>

                <p class="scene-caption-bottom">
                    Hover over the scene – the magnifying glass highlights a zoomed detail, just like a real forensic lens.
                </p>
            </div>

            <div class="scene-zones">
                <h3>Search Zones</h3>
                <form method="post" class="zone-grid">
                    <button type="submit" name="search_zone" value="desk" class="zone-card">
                        <h4>Search the Desk</h4>
                        <p>Check drawers, papers, and personal items.</p>
                    </button>
                    <button type="submit" name="search_zone" value="window" class="zone-card">
                        <h4>Check the Window</h4>
                        <p>Look for signs of exit or entry.</p>
                    </button>
                    <button type="submit" name="search_zone" value="floor" class="zone-card">
                        <h4>Inspect the Floor</h4>
                        <p>Look beneath furniture for dropped evidence.</p>
                    </button>
                </form>
                <p class="hint-text">
                    Zones you’ve already searched may still hold hidden connections to suspects.
                </p>
            </div>
        </div>
    </section>

    <?php render_evidence_bag_sidebar($caseId); ?>
    <?php render_notebook($caseId); ?>
</main>

<script>
// Magnifying glass logic
(function() {
    const wrapper = document.getElementById('sceneWrapper');
    const img     = document.getElementById('crimeSceneImage');
    const lens    = document.getElementById('magnifierLens');
    if (!wrapper || !img || !lens) return;

    const zoom = 2; // how much to zoom

    function moveLens(e) {
        const rect = wrapper.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        const lensRadius = lens.offsetWidth / 2;

        let lx = x;
        let ly = y;
        if (lx < lensRadius) lx = lensRadius;
        if (ly < lensRadius) ly = lensRadius;
        if (lx > rect.width - lensRadius) lx = rect.width - lensRadius;
        if (ly > rect.height - lensRadius) ly = rect.height - lensRadius;

        lens.style.left = lx + 'px';
        lens.style.top  = ly + 'px';

        const bgX = -((lx * zoom) - lensRadius);
        const bgY = -((ly * zoom) - lensRadius);
        lens.style.backgroundPosition = bgX + 'px ' + bgY + 'px';
    }

    function initLens() {
        lens.style.backgroundImage = "url('vanishing_bracelet_scene.png')";
        lens.style.backgroundRepeat = 'no-repeat';
        lens.style.backgroundSize = (img.width * zoom) + 'px ' + (img.height * zoom) + 'px';
    }

    img.addEventListener('load', initLens);
    if (img.complete) {
        initLens();
    }

    wrapper.addEventListener('mousemove', moveLens);
    wrapper.addEventListener('mouseenter', () => {
        lens.style.opacity = '1';
    });
    wrapper.addEventListener('mouseleave', () => {
        lens.style.opacity = '0';
    });
})();
</script>
</body>
</html>
