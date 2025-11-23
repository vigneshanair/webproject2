<?php
// leaderboard.php
require_once 'game_state.php';
require_detective();

// You can later compute real stats. For now, show current detective as example.
$solvedCount = 0;
foreach ($_SESSION['progress'] as $info) {
    if (!empty($info['completed'])) {
        $solvedCount++;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Leaderboard – Cryptic Quest</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php render_header('Leaderboard'); ?>

<main class="main-layout">
    <section class="leaderboard">
        <h2>Investigation Rankings</h2>
        <p class="section-intro">
            Compare your performance with other investigators in the precinct.
        </p>

        <table class="leaderboard-table">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Detective</th>
                    <th>Cases Solved</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                <tr class="highlight-row">
                    <td>1</td>
                    <td><?php echo htmlspecialchars($_SESSION['detective']['name'] ?? 'You'); ?></td>
                    <td><?php echo $solvedCount; ?></td>
                    <td>Your story begins here. Solve more cases to cement your legacy.</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Detective Rivera</td>
                    <td>3</td>
                    <td>Methodical, but often late to the scene.</td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>Detective Shah</td>
                    <td>2</td>
                    <td>Brilliant with forensics, hates paperwork.</td>
                </tr>
            </tbody>
        </table>
    </section>
</main>
</body>
</html>
