/* public/js/firebase-init.js */
(function () {
  function log(msg) {
    console.log("[firebase-init]", msg);
  }

  try {
    const cfg = window.FIREBASE_CONFIG;
    if (!cfg || !cfg.projectId) {
      log("FIREBASE_CONFIG missing ❌ (check index.html script order)");
      window.__firebaseInitOk = false;
      return;
    }

    if (!window.firebase) {
      log("firebase SDK not loaded ❌ (gstatic scripts missing)");
      window.__firebaseInitOk = false;
      return;
    }

    if (!firebase.apps || firebase.apps.length === 0) {
      firebase.initializeApp(cfg);
      log("initializeApp ✅ " + cfg.projectId);
    } else {
      log("already initialized ✅");
    }

    window.auth = firebase.auth();
    window.db = firebase.firestore();

    // Sign in anonymously ASAP for demo
    window.auth.onAuthStateChanged((u) => {
      if (u) {
        log("auth user ✅ " + u.uid);
        window.__firebaseAuthOk = true;
      } else {
        log("no user yet… signing in anonymously");
        window.auth.signInAnonymously().catch((e) => {
          console.error("[firebase-init] signInAnonymously error:", e);
          window.__firebaseAuthOk = false;
        });
      }
    });

    window.__firebaseInitOk = true;
  } catch (e) {
    console.error("[firebase-init] crash:", e);
    window.__firebaseInitOk = false;
  }
})();
