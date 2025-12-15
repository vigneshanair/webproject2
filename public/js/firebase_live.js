/* public/js/firebase_live.js */
(function () {
  const COLLECTION = "puzzle_events";

  function safeNow() {
    return new Date().toLocaleString();
  }

  async function ensureAuth() {
    if (!window.auth) throw new Error("window.auth missing");
    if (window.auth.currentUser) return window.auth.currentUser;
    await window.auth.signInAnonymously();
    return window.auth.currentUser;
  }

  // Subscribe and render into #liveFeed (if present)
  function subscribeToFeed(onItems) {
    const db = window.db || firebase.firestore();

    return db
      .collection(COLLECTION)
      .orderBy("createdAt", "desc")
      .limit(10)
      .onSnapshot(
        (snap) => {
          const items = snap.docs.map((d) => ({ id: d.id, ...d.data() }));
          onItems(items);
        },
        (err) => {
          console.error("[firebase_live] onSnapshot error:", err);
          const el = document.querySelector("#liveFeed");
          if (el) {
            el.innerHTML =
              `<div class="help small">Firestore error ❌ ${String(err.message || err)}<br/>(${safeNow()})</div>`;
          }
        }
      );
  }

  // This is what app.js will call after solve
  async function postPuzzleEvent(payload) {
    const db = window.db || firebase.firestore();
    await ensureAuth();

    const doc = {
      ...payload,
      createdAt: firebase.firestore.FieldValue.serverTimestamp(),
    };

    return db.collection(COLLECTION).add(doc);
  }

  // Auto-wire the feed if #liveFeed exists
  function initAutoFeed() {
    const el = document.querySelector("#liveFeed");
    if (!el) return true;

    el.innerHTML = `<div class="help small">Firebase ready… solve once to post an event.</div>`;

    subscribeToFeed((items) => {
      el.innerHTML = "";
      if (!items.length) {
        el.innerHTML = `<div class="help small">No events yet. Solve a puzzle to create one.</div>`;
        return;
      }
      items.forEach((it) => {
        const div = document.createElement("div");
        div.className = "feedItem";
        const ts =
          it.createdAt && it.createdAt.toDate ? it.createdAt.toDate().toLocaleString() : "";
        div.innerHTML = `
          <div><b>${(it.player || "Anonymous")}</b> • solved <b>${it.size}×${it.size}</b></div>
          <div>Moves: <b>${it.moves ?? "?"}</b> • Time: <b>${it.seconds ?? "?"}s</b></div>
          <small>${ts}</small>`;
        el.appendChild(div);
      });
    });

    return true;
  }

  window.postPuzzleEvent = postPuzzleEvent;
  window.FirebaseLive = { init: initAutoFeed };

  // Run after DOM is ready (safe)
  window.addEventListener("DOMContentLoaded", () => {
    try {
      initAutoFeed();
    } catch (e) {
      console.error("[firebase_live] init error:", e);
    }
  });
})();
