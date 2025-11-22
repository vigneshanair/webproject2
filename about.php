<?php
$page_title = 'About';
require_once __DIR__ . '/includes/config.php';
include __DIR__ . '/includes/header.php';
?>
<h1>About the Project</h1>
<p class="tagline">Mystery Board Game – “Cryptic Quest: Crime Scene Investigation”</p>

<section class="card-grid">
    <article class="card">
        <div class="card-title">What this game does</div>
        <ul class="simple-list">
            <li>Session-based Evidence Bag using PHP sessions</li>
            <li>Dynamic difficulty in the Forensics Lab</li>
            <li>Interconnected cases with unlock conditions</li>
            <li>Real-time leaderboard and case archive via CSV files</li>
            <li>Multiple suspect interrogations with personality traits</li>
            <li>Form-based crime reconstruction</li>
            <li>Detective Notebook for player-written theories</li>
        </ul>
    </article>

    <article class="card">
        <div class="card-title">Process &amp; Methodology</div>
        <p class="muted">Kanban-style workflow:</p>
        <ul class="simple-list">
            <li><strong>Backlog:</strong> Evidence Bag, forensic mini-game, interrogations, reconstruction, leaderboard.</li>
            <li><strong>In Progress:</strong> Case unlock logic, notebook feature, UI refinements.</li>
            <li><strong>Done:</strong> Fully playable investigation flow with two interconnected cases.</li>
        </ul>
    </article>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
