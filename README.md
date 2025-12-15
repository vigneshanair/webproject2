<<<<<<< HEAD
Cryptic Quest: Crime Scene Investigation
CSC 4370/6370 — Web Programming
Fall 2025
Team Member:
• Vignesh Ajith Nair
1. Project Overview
Cryptic Quest is an interactive mystery investigation game where the player steps into the role of a detective solving crime cases. Each case contains clues, suspects, evidence, and multiple paths the player can take. As the investigation progresses, new information appears and the case file evolves.
The game focuses on exploration, deduction, and piecing together events from clues found across different modules.
2. Chosen Project Topic
Mystery Board Game – “Cryptic Quest: Crime Scene Investigation”
(Selected from the official list of project themes)
3. Core Features
✔ Evidence Collection
Clues discovered in different pages are stored using PHP sessions, forming the player’s evidence bag.
✔ Difficulty Scaling
The difficulty for each case adjusts based on the player’s progress and accuracy.
✔ Interconnected Cases
Cases are stored in the database and are linked through shared progress, evidence, and story flow.
✔ Leaderboard
Player performance (score, completion) is tracked and shown through a leaderboard.
4. Additional Features
These were required for graduate-level submissions, and all are included:
✔ Case File System
A dynamic case file that updates as clues, notes, and suspect info are added.
✔ Forensic Lab
A small analysis tool that gives a match score for collected evidence.
✔ Suspect Interrogations
Each suspect has unique dialogue, personality traits, and truth/lie indicators.
✔ Crime Scene Reconstruction
A drag-and-drop style reconstruction activity where the player must place evidence in the correct positions.
5. Tech Stack
HTML & CSS for layout and styling
PHP (server-side) for session handling, logic, and data flow
MariaDB for storing cases, player progress, and leaderboard info
=======
# 🎄 Santa's Workshop — Christmas Fifteen Puzzle (Version 1)

From-scratch implementation:
- Adaptive difficulty (performance-based)
- Multi-size boards 3×3 up to 10×10
- Image-slice tiles (not numbers) + optional number overlay
- Magic hint system (limited)
- Timer, moves, progress bar, victory effects
- Database logging: users, puzzles, sessions, events, analytics, leaderboard, dev journal
- Security: prepared statements + sessions + CSRF header token

## Setup (MySQL via CLI only)

1) Create DB and user:
```bash
mysql -u YOUR_ADMIN -p
CREATE DATABASE YOUR_DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL PRIVILEGES ON YOUR_DB_NAME.* TO 'YOUR_DB_USER'@'localhost' IDENTIFIED BY 'YOUR_DB_PASSWORD';
FLUSH PRIVILEGES;
EXIT;
```

2) Import schema:
```bash
mysql -u YOUR_DB_USER -p YOUR_DB_NAME < sql/schema.sql
```

3) Configure `api/config.php` (DB_* constants)

4) Deploy and open `index.html`

Leaderboards + Journal require login.
>>>>>>> 1febf57 (Finalize Santa Fifteen Puzzle: Firebase live feed + assets + fixes)
