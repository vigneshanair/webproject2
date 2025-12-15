// public/js/app.js
(function () {
  const $ = (s) => document.querySelector(s);

  // ✅ API base:
  // - On CODD: use relative "api"
  // - Elsewhere (Firebase): use absolute CODD path (no more 404)
  const API_BASE = location.hostname.includes("codd.cs.gsu.edu")
    ? "api"
    : "https://codd.cs.gsu.edu/~vajithnair1/webproject3/christmas_fifteen_puzzle_v1/api";

  // Required UI
  const boardEl = $("#board");
  const movesEl = $("#moves");
  const timeEl = $("#time");
  const statusEl = $("#status");
  const badgeEl = $("#winBadge");

  // Controls
  const themeSelect = $("#themeSelect");
  const sizeSelect = $("#sizeSelect");

  const btnNew = $("#btnNew");
  const btnShuffle = $("#btnShuffle");
  const btnHint = $("#btnHint");
  const btnNumbers = $("#btnNumbers");
  const btnSound = $("#btnSound");
  const btnPreview = $("#btnPreview");
  const btnClosePreview = $("#btnClosePreview");
  const btnReset = $("#btnReset");
  const btnSave = $("#btnSave");
  const btnLoadLB = $("#btnLoadLB");

  // Modals
  const previewModal = $("#previewModal");
  const previewImage = $("#previewImage");

  const hintModal = $("#hintModal");
  const btnCloseHint = $("#btnCloseHint");
  const hintText = $("#hintText");

  // Side panels
  const liveFeedEl = $("#liveFeed");
  const lbEl = $("#leaderboard");
  const playerNameEl = $("#playerName");

  // Intro modal
  const introModal = $("#introModal");
  const btnStartGame = $("#btnStartGame");
  const btnSkipIntro = $("#btnSkipIntro");
  const introName = $("#introName");
  const introDifficulty = $("#introDifficulty");
  const introTheme = $("#introTheme");
  const introSound = $("#introSound");
  const introNumbers = $("#introNumbers");
  const introSize = $("#introSize");

  let showNumbers = true;
  let soundOn = true;

  let timer = null;
  let startMs = null;

  function setStatus(msg) {
    if (statusEl) statusEl.textContent = msg;
    console.log("[STATUS]", msg);
  }

  function fmtTime(ms) {
    const s = Math.max(0, Math.floor(ms / 1000));
    const m = Math.floor(s / 60);
    const r = s % 60;
    return String(m).padStart(2, "0") + ":" + String(r).padStart(2, "0");
  }

  function startTimer() {
    if (timer) return;
    startMs = Date.now();
    timer = setInterval(() => {
      if (timeEl) timeEl.textContent = fmtTime(Date.now() - startMs);
    }, 250);
  }

  function stopTimer() {
    if (!timer) return;
    clearInterval(timer);
    timer = null;
  }

  function resetTimer() {
    stopTimer();
    startMs = null;
    if (timeEl) timeEl.textContent = "00:00";
  }

  function secondsElapsed() {
    if (!startMs) return 0;
    return Math.floor((Date.now() - startMs) / 1000);
  }

  // -------------------------
  // Puzzle adapter
  // -------------------------
  function P() {
    return window.Puzzle;
  }
  function puzzleInit(n) {
    const p = P();
    if (!p) throw new Error("Puzzle engine not loaded (js/puzzle.js)");
    if (typeof p.reset === "function") return p.reset(n);
    if (typeof p.init === "function") return p.init(n);
    throw new Error("Puzzle engine missing reset()/init()");
  }
  function puzzleShuffle(steps) {
    const p = P();
    if (typeof p.shuffle === "function") return p.shuffle(steps);
    throw new Error("Puzzle engine missing shuffle()");
  }
  function puzzleTryMove(val) {
    return P().tryMove(val);
  }
  function puzzleIsSolved() {
    return P().isSolved();
  }
  function puzzleState() {
    return P().getState();
  }
  function puzzleMoves() {
    const p = P();
    if (typeof p.getMoves === "function") return p.getMoves();
    return puzzleState().moves ?? 0;
  }

  // -------------------------
  // Theme + image (robust for assets/ + case)
  // -------------------------
  function tryLoadImage(src) {
    return new Promise((resolve) => {
      const img = new Image();
      img.onload = () => resolve({ ok: true, src });
      img.onerror = () => resolve({ ok: false, src });
      img.src = src;
    });
  }

  function buildThemeCandidates(fileName) {
    const raw = String(fileName || "").trim().replace(/^(\.\/)+/, "");
    if (!raw) return [];

    const baseName = raw.split("/").pop(); // keep only filename
    const lower = baseName.toLowerCase();

    const candidates = [];

    // If user passed "assets/xxx", try as-is first
    if (raw.startsWith("assets/")) candidates.push(raw);

    // Your project uses assets/<file>
    candidates.push(`assets/${baseName}`);
    candidates.push(`assets/${lower}`);

    // optional fallback if someone put in assets/images/
    candidates.push(`assets/images/${baseName}`);
    candidates.push(`assets/images/${lower}`);

    // de-dupe
    return [...new Set(candidates)];
  }

  async function setTheme(fileName) {
    const candidates = buildThemeCandidates(fileName);

    for (const path of candidates) {
      const t = await tryLoadImage(path);
      if (t.ok) {
        document.documentElement.style.setProperty("--puzzle-img", `url("${path}")`);
        if (previewImage) previewImage.src = path;
        setStatus(`Theme loaded ✅ (${path})`);
        return;
      }
    }

    setStatus("Image not found ❌ Put it in public/assets/ and match name exactly.");
    document.documentElement.style.setProperty(
      "--puzzle-img",
      `linear-gradient(135deg, rgba(255,59,92,.28), rgba(34,197,94,.18))`
    );
    if (previewImage) previewImage.removeAttribute("src");
  }

  function applyGridSize(n) {
    if (!boardEl) return;
    boardEl.style.gridTemplateColumns = `repeat(${n}, 1fr)`;
    boardEl.style.gridTemplateRows = `repeat(${n}, 1fr)`;
  }

  // -------------------------
  // Render board
  // -------------------------
  function render(highlightVal = null) {
    try {
      if (!boardEl) return setStatus("Missing #board ❌ (index.html mismatch)");
      const st = puzzleState();
      if (!st) return setStatus("Puzzle state missing ❌");

      const n = st.size;
      applyGridSize(n);
      boardEl.innerHTML = "";

      const boardPx = boardEl.getBoundingClientRect().width;
      if (!boardPx || boardPx < 10) {
        requestAnimationFrame(() => render(highlightVal));
        return;
      }
      const tilePx = boardPx / n;

      for (let i = 0; i < st.tiles.length; i++) {
        const val = st.tiles[i];
        const tile = document.createElement("div");
        tile.className = "tile";

        if (!showNumbers) tile.classList.add("hideNum");
        if (val === 0) {
          tile.classList.add("empty");
          boardEl.appendChild(tile);
          continue;
        }

        const correctIndex = val - 1;
        const cx = correctIndex % n;
        const cy = Math.floor(correctIndex / n);

        tile.style.backgroundSize = `${boardPx}px ${boardPx}px`;
        tile.style.backgroundPosition = `${-(cx * tilePx)}px ${-(cy * tilePx)}px`;

        const num = document.createElement("div");
        num.className = "num";
        num.textContent = String(val);
        tile.appendChild(num);

        if (highlightVal && val === highlightVal) {
          tile.style.outline = "2px solid rgba(247,201,72,.75)";
          tile.style.boxShadow = "0 0 0 6px rgba(247,201,72,.12)";
        }

        tile.addEventListener("click", () => {
          window.AudioFX?.unlockOnce?.();

          const moved = puzzleTryMove(val);
          if (!moved) return;

          if (!startMs) startTimer();
          if (movesEl) movesEl.textContent = String(puzzleMoves());
          if (soundOn) window.AudioFX?.playMove?.();

          render();

          if (puzzleIsSolved()) {
            if (badgeEl) {
              badgeEl.textContent = "Solved 🎉";
              badgeEl.classList.add("win");
            }
            stopTimer();
            if (soundOn) window.AudioFX?.playWin?.();
            setStatus("Solved! Firebase event will post (if configured).");
            tryPostFirebaseSolve();
          } else {
            if (badgeEl) {
              badgeEl.textContent = "Playing";
              badgeEl.classList.remove("win");
            }
          }
        });

        boardEl.appendChild(tile);
      }
    } catch (e) {
      console.error(e);
      setStatus(`Render error ❌ ${e.message}`);
    }
  }

  // -------------------------
  // Hint
  // -------------------------
  function manhattanSum(tiles, n) {
    let sum = 0;
    for (let i = 0; i < tiles.length; i++) {
      const v = tiles[i];
      if (!v) continue;
      const goal = v - 1;
      const r1 = Math.floor(i / n), c1 = i % n;
      const r2 = Math.floor(goal / n), c2 = goal % n;
      sum += Math.abs(r1 - r2) + Math.abs(c1 - c2);
    }
    return sum;
  }

  function bestHintMove() {
    const st = puzzleState();
    const n = st.size;
    const tiles = st.tiles.slice();

    const e = tiles.indexOf(0);
    const er = Math.floor(e / n), ec = e % n;

    const neighborIdx = [];
    if (er > 0) neighborIdx.push(e - n);
    if (er < n - 1) neighborIdx.push(e + n);
    if (ec > 0) neighborIdx.push(e - 1);
    if (ec < n - 1) neighborIdx.push(e + 1);

    let best = null;
    for (const idx of neighborIdx) {
      const copy = tiles.slice();
      const tmp = copy[idx];
      copy[idx] = 0;
      copy[e] = tmp;

      const score = manhattanSum(copy, n);
      if (!best || score < best.score) best = { val: tmp, score };
    }
    return best;
  }

  function openHintModal() {
    const best = bestHintMove();
    if (!best) {
      hintText.textContent = "No hint available yet — try Shuffle first.";
      hintModal.classList.remove("hidden");
      return;
    }

    hintText.innerHTML =
      `✅ Best next move: <b>slide tile ${best.val}</b> into the empty space.<br/><br/>
       I highlighted the recommended tile for you.`;

    hintModal.classList.remove("hidden");
    render(best.val);
  }

  function closeHintModal() {
    hintModal.classList.add("hidden");
    render(null);
  }

  // -------------------------
  // Firebase support (FirebaseLive OR postPuzzleEvent)
  // -------------------------
  let firebaseMode = "none"; // "FirebaseLive" | "postPuzzleEvent" | "none"

  function tryInitFirebase() {
    try {
      if (window.FirebaseLive?.init) {
        const ok = window.FirebaseLive.init();
        if (ok) {
          firebaseMode = "FirebaseLive";
          window.FirebaseLive.subscribe?.((items) => {
            if (!liveFeedEl) return;
            liveFeedEl.innerHTML = "";
            if (!items || !items.length) {
              liveFeedEl.innerHTML = `<div class="help small">No events yet. Solve a puzzle to post one.</div>`;
              return;
            }
            items.forEach((it) => {
              const div = document.createElement("div");
              div.className = "feedItem";
              const ts = it.ts && it.ts.toDate ? it.ts.toDate().toLocaleString() : "";
              div.innerHTML = `
                <div><b>${esc(it.player || "Anonymous")}</b> solved <b>${it.size}×${it.size}</b></div>
                <div>Moves: <b>${it.moves ?? "?"}</b> • Time: <b>${it.seconds ?? "?"}s</b></div>
                <small>${esc(ts)}</small>`;
              liveFeedEl.appendChild(div);
            });
          });
          setStatus("Firebase enabled ✅");
          return;
        }
      }

      if (typeof window.postPuzzleEvent === "function") {
        firebaseMode = "postPuzzleEvent";
        setStatus("Firebase enabled ✅");
        return;
      }

      firebaseMode = "none";
      if (liveFeedEl) {
        liveFeedEl.innerHTML = `<div class="help small">Firebase not ready. Check console + Auth/Firestore enabled.</div>`;
      }
    } catch (e) {
      console.warn("Firebase init error:", e);
      firebaseMode = "none";
    }
  }

  function tryPostFirebaseSolve() {
    try {
      const st = puzzleState();
      const player = (playerNameEl?.value || "").trim() || "Anonymous";

      const payload = {
        player,
        size: st?.size ?? 4,
        moves: puzzleMoves(),
        seconds: secondsElapsed(),
        theme: themeSelect?.value || "unknown",
      };

      if (firebaseMode === "FirebaseLive") {
        window.FirebaseLive.pushSolveEvent?.(payload);
      } else if (firebaseMode === "postPuzzleEvent") {
        window.postPuzzleEvent({
          type: "solve",
          ...payload,
          message: `${payload.player} solved ${payload.size}×${payload.size} in ${payload.moves} moves (${payload.seconds}s)`,
        });
      }
    } catch (e) {
      console.warn("Firebase post error:", e);
    }
  }

  // -------------------------
  // MySQL API (CODD)
  // -------------------------
  async function apiGet(url) {
    const res = await fetch(url, { credentials: "include" });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    return res.json();
  }
  async function apiPost(url, body) {
    const res = await fetch(url, {
      method: "POST",
      credentials: "include",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(body || {}),
    });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    return res.json();
  }

  async function loadLeaderboard() {
    if (!lbEl) return;

    lbEl.innerHTML = `<div class="help small">Loading…</div>`;
    try {
      const url = `${API_BASE}/leaderboard.php?limit=10`;
      const data = await apiGet(url);

      if (!data.ok) throw new Error(data.error || "Unknown error");
      lbEl.innerHTML = "";

      if (!data.rows || !data.rows.length) {
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
      lbEl.innerHTML = `<div class="help small">
        Leaderboard not reachable from this URL. Open on CODD to demo MySQL.<br/>
        <small>${esc(e.message)}</small>
      </div>`;
    }
  }

  async function saveToDB() {
    try {
      const player = (playerNameEl?.value || "").trim();
      if (!player) return setStatus("Enter Player Name before saving.");
      if (!puzzleIsSolved()) return setStatus("Solve first, then Save to DB.");

      const payload = {
        player,
        size: puzzleState().size,
        moves: puzzleMoves(),
        seconds: secondsElapsed(),
        theme: themeSelect?.value || "unknown",
      };

      const url = `${API_BASE}/session_end.php`;
      const data = await apiPost(url, payload);

      if (!data.ok) throw new Error(data.error || "Save failed");
      setStatus("Saved to MySQL ✅");
      loadLeaderboard();
    } catch (e) {
      setStatus(`DB save failed: ${e.message}`);
    }
  }

  function esc(s) {
    return String(s ?? "")
      .replaceAll("&", "&amp;")
      .replaceAll("<", "&lt;")
      .replaceAll(">", "&gt;")
      .replaceAll('"', "&quot;")
      .replaceAll("'", "&#039;");
  }

  // -------------------------
  // Intro modal logic
  // -------------------------
  function closeIntro() {
    introModal?.classList.add("hidden");
  }

  async function applyIntroPrefsAndStart() {
    if (playerNameEl && introName?.value?.trim()) playerNameEl.value = introName.value.trim();

    const n = parseInt(introSize?.value || sizeSelect?.value || "4", 10);
    if (sizeSelect) sizeSelect.value = String(n);

    const th = introTheme?.value || themeSelect?.value || "santa.jpg";
    if (themeSelect) themeSelect.value = th;

    showNumbers = !!introNumbers?.checked;
    if (btnNumbers) btnNumbers.textContent = `Numbers: ${showNumbers ? "On" : "Off"}`;

    soundOn = !!introSound?.checked;
    window.AudioFX?.setEnabled?.(soundOn);
    if (btnSound) btnSound.textContent = `Sound: ${soundOn ? "On" : "Off"}`;
    if (soundOn) window.AudioFX?.unlockOnce?.();

    resetTimer();
    puzzleInit(n);
    await setTheme(th);

    const diff = introDifficulty?.value || "medium";
    const steps = diff === "easy" ? n * n * 10 : diff === "hard" ? n * n * 45 : n * n * 25;
    puzzleShuffle(steps);

    if (movesEl) movesEl.textContent = "0";
    if (badgeEl) {
      badgeEl.textContent = "Playing";
      badgeEl.classList.remove("win");
    }

    render();
    setStatus("Shuffled ✅ Start solving!");
  }

  // -------------------------
  // Init + wiring
  // -------------------------
  async function init() {
    try {
      if (!P()) {
        setStatus("Missing puzzle.js ❌ (check <script defer src='js/puzzle.js'>)");
        return;
      }
      if (!boardEl) {
        setStatus("Missing #board ❌ (index.html mismatch)");
        return;
      }

      const n = parseInt(sizeSelect?.value || "4", 10);
      puzzleInit(n);
      await setTheme(themeSelect?.value || "santa.jpg");
      render();
      setStatus("Ready ✅ Use Start Game to begin.");

      // Firebase safe init
      tryInitFirebase();

      // Buttons
      btnNew?.addEventListener("click", async () => {
        resetTimer();
        puzzleInit(parseInt(sizeSelect?.value || "4", 10));
        if (movesEl) movesEl.textContent = "0";
        if (badgeEl) { badgeEl.textContent = "Playing"; badgeEl.classList.remove("win"); }
        await setTheme(themeSelect?.value || "santa.jpg");
        render();
        setStatus("New board created.");
      });

      btnShuffle?.addEventListener("click", () => {
        resetTimer();
        const nn = parseInt(sizeSelect?.value || "4", 10);
        puzzleInit(nn);
        puzzleShuffle(nn * nn * 25);
        if (movesEl) movesEl.textContent = "0";
        if (badgeEl) { badgeEl.textContent = "Playing"; badgeEl.classList.remove("win"); }
        render();
        setStatus("Shuffled ✅ Start solving!");
      });

      btnReset?.addEventListener("click", () => {
        resetTimer();
        const p = P();
        if (typeof p.restoreInitial === "function") {
          p.restoreInitial();
        } else {
          const nn = parseInt(sizeSelect?.value || "4", 10);
          puzzleInit(nn);
          puzzleShuffle(nn * nn * 25);
        }
        if (movesEl) movesEl.textContent = "0";
        if (badgeEl) { badgeEl.textContent = "Playing"; badgeEl.classList.remove("win"); }
        render();
        setStatus("Reset ✅");
      });

      btnHint?.addEventListener("click", () => openHintModal());

      btnNumbers?.addEventListener("click", () => {
        showNumbers = !showNumbers;
        btnNumbers.textContent = `Numbers: ${showNumbers ? "On" : "Off"}`;
        render();
      });

      btnSound?.addEventListener("click", () => {
        soundOn = !soundOn;
        window.AudioFX?.setEnabled?.(soundOn);
        btnSound.textContent = `Sound: ${soundOn ? "On" : "Off"}`;
        setStatus(soundOn ? "Christmas sounds enabled 🔔" : "Sound muted.");
      });

      themeSelect?.addEventListener("change", async () => {
        await setTheme(themeSelect.value);
        render();
      });

      sizeSelect?.addEventListener("change", () => {
        const nn = parseInt(sizeSelect.value, 10);
        resetTimer();
        puzzleInit(nn);
        puzzleShuffle(nn * nn * 25);
        if (movesEl) movesEl.textContent = "0";
        render();
        setStatus(`Board set to ${nn}×${nn}.`);
      });

      // Preview
      btnPreview?.addEventListener("click", () => previewModal?.classList.remove("hidden"));
      btnClosePreview?.addEventListener("click", () => previewModal?.classList.add("hidden"));
      previewModal?.addEventListener("click", (e) => {
        if (e.target === previewModal) previewModal.classList.add("hidden");
      });

      // Hint modal
      btnCloseHint?.addEventListener("click", closeHintModal);
      hintModal?.addEventListener("click", (e) => {
        if (e.target === hintModal) closeHintModal();
      });

      // DB
      btnSave?.addEventListener("click", saveToDB);
      btnLoadLB?.addEventListener("click", loadLeaderboard);
      loadLeaderboard();

      // Intro
      btnStartGame?.addEventListener("click", async () => {
        closeIntro();
        await applyIntroPrefsAndStart();
      });
      btnSkipIntro?.addEventListener("click", closeIntro);

      window.setTimeout(() => introName?.focus?.(), 150);
    } catch (e) {
      console.error(e);
      setStatus(`Init error ❌ ${e.message}`);
    }
  }

  window.addEventListener("DOMContentLoaded", init);
})();
