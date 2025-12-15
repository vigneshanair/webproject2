-- Santa's Workshop (Version 1) — MySQL Schema
-- Run (CLI): mysql -u USER -p DBNAME < sql/schema.sql

SET NAMES utf8mb4;
SET time_zone = '+00:00';

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(24) NOT NULL,
  email VARCHAR(190) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_users_username (username),
  UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS profiles (
  user_id INT PRIMARY KEY,
  preferred_theme VARCHAR(20) NOT NULL DEFAULT 'auto',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS user_skill (
  user_id INT PRIMARY KEY,
  skill_rating DOUBLE NOT NULL DEFAULT 0.0,
  difficulty_level INT NOT NULL DEFAULT 1,
  games_played INT NOT NULL DEFAULT 0,
  last_played_at TIMESTAMP NULL DEFAULT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS puzzles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  size_n INT NOT NULL,
  difficulty_rating INT NOT NULL,
  shuffle_steps INT NOT NULL,
  initial_state_json JSON NOT NULL,
  solution_path_json JSON NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_puzzles_size_diff (size_n, difficulty_rating)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS game_sessions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  puzzle_id INT NOT NULL,
  size_n INT NOT NULL,
  difficulty_rating INT NOT NULL,
  status ENUM('active','completed','abandoned') NOT NULL DEFAULT 'active',
  started_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  finished_at TIMESTAMP NULL DEFAULT NULL,
  moves INT NOT NULL DEFAULT 0,
  elapsed_s INT NOT NULL DEFAULT 0,
  magic_left INT NOT NULL DEFAULT 0,
  current_state_json JSON NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
  FOREIGN KEY (puzzle_id) REFERENCES puzzles(id) ON DELETE CASCADE,
  KEY idx_sessions_user_status (user_id, status),
  KEY idx_sessions_size_diff (size_n, difficulty_rating)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS session_events (
  id INT AUTO_INCREMENT PRIMARY KEY,
  session_id INT NOT NULL,
  event_type VARCHAR(40) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (session_id) REFERENCES game_sessions(id) ON DELETE CASCADE,
  KEY idx_session_events (session_id, event_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS leaderboard (
  user_id INT NOT NULL,
  size_n INT NOT NULL,
  best_time_s INT NOT NULL DEFAULT 999999,
  best_moves INT NOT NULL DEFAULT 999999,
  best_difficulty INT NOT NULL DEFAULT 1,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, size_n),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  KEY idx_leaderboard_size_time (size_n, best_time_s),
  KEY idx_leaderboard_size_moves (size_n, best_moves)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS analytics_events (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  session_id INT NOT NULL,
  event_type VARCHAR(30) NOT NULL,
  size_n INT NOT NULL,
  difficulty_rating INT NOT NULL,
  moves INT NOT NULL,
  elapsed_s INT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
  FOREIGN KEY (session_id) REFERENCES game_sessions(id) ON DELETE CASCADE,
  KEY idx_analytics_type_date (event_type, created_at),
  KEY idx_analytics_user_date (user_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS achievements (
  code VARCHAR(32) PRIMARY KEY,
  name VARCHAR(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS user_achievements (
  user_id INT NOT NULL,
  achievement_code VARCHAR(32) NOT NULL,
  unlocked_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, achievement_code),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (achievement_code) REFERENCES achievements(code) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS dev_journal (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  entry TEXT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  KEY idx_journal_user_date (user_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
