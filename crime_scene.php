<?php
$page_title = 'Crime Scene';
require_once __DIR__ . '/includes/config.php';

$case = get_active_case();
if (!$case) {
    header('Location: cases.php');
    exit;
}

$message = '';
$areaData = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $area_key = $_POST['area'] ?? '';
    if (isset($case['crime_scene_areas'][$area_key])) {
        $areaData = $case['crime_scene_areas'][$area_key];
        add_evidence($areaData['clue']);
        $message = 'You examined the ' . $areaData['title'] . ' and recorded a new clue.';
    }
}

include __DIR__ . '/includes/header.php';
?>
<div class="screen-header">
    <h1>Crime Scene Board</h1>
    <p class="tagline">Choose an area to inspect and log anything that stands out in your evidence bag.</p>
</div>

<section class="board-layout">
    <article class="panel panel-main">
        <h2>Scene Locations</h2>
        <form method="post" class="form-card">
            <div class="form-group">
                <label for="area">Inspect area</label>
                <select id="area" name="area">
                    <?php foreach ($case['crime_scene_areas'] as $key => $area): ?>
                        <option value="<?php echo htmlspecialchars($key); ?>">
                            <?php echo htmlspecialchars($area['title']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn">Examine Location</button>
        </form>

        <?php if ($areaData): ?>
            <div class="note-card">
                <strong><?php echo htmlspecialchars($areaData['title']); ?></strong>
                <p><?php echo htmlspecialchars($areaData['description']); ?></p>
                <p><strong>Logged clue:</strong> <?php echo htmlspecialchars($areaData['clue']); ?></p>
            </div>
        <?php elseif ($message): ?>
            <p><?php echo htmlspecialchars($message); ?></p>
        <?php else: ?>
            <p class="muted">Pick a location to see immediate notes from that area.</p>
        <?php endif; ?>
    </article>

    <aside class="panel panel-side">
        <h2>Evidence Bag</h2>
        <div class="evidence-panel">
            <?php if (empty($_SESSION['evidence_bag'])): ?>
                <p class="muted">No evidence yet. Start with the main entry, roof, or security room.</p>
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
