// js/audio.js
(function () {
  // Keep filenames the same; replace MP3 files if you want
  const MOVE_SFX = "assets/audio/move.mp3";
  const WIN_SFX  = "assets/audio/win.mp3";

  const move = new Audio(MOVE_SFX);
  const win  = new Audio(WIN_SFX);

  move.preload = "auto";
  win.preload  = "auto";

  move.volume = 0.55;
  win.volume  = 0.70;

  let enabled = true;
  let unlocked = false;

  function prime() {
    try {
      move.currentTime = 0;
      move.play().then(() => {
        move.pause();
        move.currentTime = 0;
      }).catch(() => {});
    } catch {}

    try {
      win.currentTime = 0;
      win.play().then(() => {
        win.pause();
        win.currentTime = 0;
      }).catch(() => {});
    } catch {}
  }

  function unlockOnce() {
    if (unlocked) return;
    unlocked = true;
    prime();
  }

  function playMove() {
    if (!enabled) return;
    try {
      unlockOnce();
      move.currentTime = 0;
      move.play().catch(() => {});
    } catch {}
  }

  function playWin() {
    if (!enabled) return;
    try {
      unlockOnce();
      win.currentTime = 0;
      win.play().catch(() => {});
    } catch {}
  }

  function setEnabled(on) {
    enabled = !!on;
    if (enabled) unlockOnce();
  }

  window.addEventListener(
    "pointerdown",
    () => unlockOnce(),
    { once: true, capture: true }
  );

  window.AudioFX = { prime, unlockOnce, playMove, playWin, setEnabled };
})();
