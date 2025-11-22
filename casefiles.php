<?php
$page_title = 'Case Files';
require_once __DIR__ . '/includes/config.php';

$rows = [];
$path = __DIR__ . '/data/solved_cases.csv';

if (file_exists($path) && ($fp = fopen($path, 'r')) !== false) {
    while (($data = fgetcsv($fp)) !== false) {
        if (count($data) >= 5) {
            $rows[] = [
                'time'  => $data[0],
                'name'  => $data[1],
                'id'    => $data[2],
                'title' => $data[3],
                'score' => (int)$data[4],
            ];
        }
    }
    fclose($fp);
}

include __DIR__ . '/includes/header.php';
?>
<h1>Case Files Archive</h1>
<p class="tagline">Completed investigations and who solved them.</p>

<article class="card">
    <?php if (empty($rows)): ?>
        <p class="muted">No cases solved yet.</p>
    <?php else: ?>
        <ul class="simple-list">
            <?php foreach (array_reverse($rows) as $r): ?>
                <li>
                    <strong><?php echo htmlspecialchars($r['title']); ?></strong>
                    (<?php echo htmlspecialchars($r['id']); ?>)<br>
                    Solved by <?php echo htmlspecialchars($r['name']); ?> –
                    Score: <?php echo $r['score']; ?> –
                    <span class="muted"><?php echo htmlspecialchars($r['time']); ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</article>
<?php include __DIR__ . '/includes/footer.php'; ?>
