<?php
$page_title = 'Interrogations – Cryptic Quest';
require_once __DIR__ . '/includes/config.php';

$caseId = get_active_case_id();
$case   = get_active_case();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $note = trim($_POST['quick_note'] ?? '');
    if ($note !== '') {
        $_SESSION['interrogation_notes'][] = $note;
    }
    header('Location: interrogations.php');
    exit;
}

include __DIR__ . '/includes/header.php';

$suspect  = $case['suspect']  ?? 'the suspect';
$location = $case['location'] ?? 'the scene';
?>

<section>
    <div class="page-header">
        <h1>Interrogations</h1>
        <p class="page-subtitle">
            Ask focused questions and capture short notes as you go.
        </p>
    </div>

    <section class="card-grid">
        <!-- Quick note card -->
        <article class="card form-card" style="max-width:none;">
            <div class="card-title">Quick Note</div>
            <p class="card-subtitle">Short summary from the latest interrogation.</p>

            <form method="post">
                <div class="form-group">
                    <label for="quick_note">Note</label>
                    <textarea id="quick_note"
                              name="quick_note"
                              placeholder="Short summary of the interrogation..."></textarea>
                </div>
                <button type="submit" class="btn">Add Note</button>
            </form>
        </article>

        <!-- Suggested questions card -->
        <article class="card">
            <div class="card-title">Suggested Questions</div>
            <p class="card-subtitle">
                Use these prompts to pressure <?php echo htmlspecialchars($suspect); ?>.
            </p>
            <ul style="font-size:0.85rem; color:var(--text-soft); padding-left:1.2rem;">
                <li>“Walk me through your exact movements near <?php echo htmlspecialchars($location); ?>.”</li>
                <li>“Who can confirm seeing you between the key timestamps?”</li>
                <li>“Why does your story differ from the camera timeline?”</li>
                <li>“Explain your connection to any missing items or people.”</li>
                <li>“Is there any reason your fingerprints would appear on the evidence?”</li>
                <li>“What did you notice that felt out of place that night?”</li>
            </ul>
        </article>
    </section>

    <?php if (!empty($_SESSION['interrogation_notes'])): ?>
        <section style="margin-top:1.4rem;">
            <article class="card">
                <div class="card-title">Recent Notes</div>
                <ul class="notes-list">
                    <?php foreach (array_reverse($_SESSION['interrogation_notes']) as $note): ?>
                        <li><?php echo htmlspecialchars($note); ?></li>
                    <?php endforeach; ?>
                </ul>
            </article>
        </section>
    <?php endif; ?>
</section>

<!-- Floating notebook button -->
<button type="button" class="floating-notes" id="open-notes" aria-label="Open notebook">
    📓
</button>

<!-- Notebook popup -->
<div class="notes-backdrop" id="notes-backdrop"></div>
<div class="notes-modal" id="notes-modal">
    <div class="notes-modal-content">
        <div class="notes-modal-header">
            <h2>Notebook</h2>
            <button type="button" data-close-notes>&times;</button>
        </div>

        <form method="post">
            <div class="form-group">
                <label for="note_text">Add a note</label>
                <textarea id="note_text"
                          name="quick_note"
                          placeholder="Write a quick observation..."></textarea>
            </div>
            <div style="display:flex; gap:0.5rem; justify-content:flex-end; margin-top:0.3rem;">
                <button type="button" class="btn btn-secondary" data-close-notes>Close</button>
                <button type="submit" class="btn">Save</button>
            </div>
        </form>

        <?php if (!empty($_SESSION['interrogation_notes'])): ?>
            <hr style="border-color:rgba(15,23,42,0.9); margin:0.7rem 0;">
            <div style="font-size:0.8rem; color:var(--text-soft);">
                <strong>Notebook entries</strong>
                <ul class="notes-list">
                    <?php foreach (array_reverse($_SESSION['interrogation_notes']) as $note): ?>
                        <li><?php echo htmlspecialchars($note); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const openBtn   = document.getElementById('open-notes');
    const backdrop  = document.getElementById('notes-backdrop');
    const modal     = document.getElementById('notes-modal');
    const closeEls  = document.querySelectorAll('[data-close-notes]');

    if (!openBtn || !backdrop || !modal) return;

    function openNotes() {
        backdrop.classList.add('is-visible');
        modal.classList.add('is-visible');
    }

    function closeNotes() {
        backdrop.classList.remove('is-visible');
        modal.classList.remove('is-visible');
    }

    openBtn.addEventListener('click', openNotes);
    backdrop.addEventListener('click', closeNotes);
    closeEls.forEach(function (el) {
        el.addEventListener('click', closeNotes);
    });
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
