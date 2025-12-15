async function loadLeaderboard() {
  if (!lbEl) return;

  const host = location.hostname || "";
  const onCodd = host.includes("codd.cs.gsu.edu") || host.includes(".cs.gsu.edu");
  if (!onCodd) {
    lbEl.innerHTML = `<div class="help small">
      Leaderboard (MySQL) works on <b>CODD (PHP server)</b>. Firebase Hosting can’t run <code>api/*.php</code>.
    </div>`;
    return;
  }

  lbEl.innerHTML = `<div class="help small">Loading…</div>`;
  try {
    const data = await apiGet("api/leaderboard.php?limit=10");
    if (!data.ok) throw new Error(data.error || "Unknown error");

    lbEl.innerHTML = "";
    if (!data.rows.length) {
      lbEl.innerHTML = `<div class="help small">No records yet.</div>`;
      return;
    }

    data.rows.forEach((r, idx) => {
      const row = document.createElement("div");
      row.className = "lbRow";
      row.innerHTML = `
        <div><b>#${idx + 1}</b> ${esc(r.player)}</div>
        <div><span>${r.size}×${r.size}</span> • <b>${r.moves}</b> moves • <b>${r.seconds}s</b></div>`;
      lbEl.appendChild(row);
    });
  } catch (e) {
    lbEl.innerHTML = `<div class="help small">Could not load leaderboard. ${esc(e.message)}</div>`;
  }
}
