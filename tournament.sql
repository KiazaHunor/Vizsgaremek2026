CREATE TABLE `tournament_entry_players` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entry_id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL,
  `slot_code` varchar(20) NOT NULL,
  `is_starter` tinyint(1) NOT NULL DEFAULT 1,
  `chemistry_points` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),

  PRIMARY KEY (`id`),

  KEY `idx_entry_id` (`entry_id`),
  KEY `idx_player_id` (`player_id`),

  CONSTRAINT `fk_entry_players_entry`
    FOREIGN KEY (`entry_id`) REFERENCES `tournament_entries` (`id`)
    ON DELETE CASCADE,

  CONSTRAINT `fk_entry_players_player`
    FOREIGN KEY (`player_id`) REFERENCES `players` (`id`)
    ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;


CREATE TABLE `tournament_entries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tournament_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `team_name` varchar(255) DEFAULT NULL,
  `chemistry_score` int(11) NOT NULL DEFAULT 0,
  `rating_avg_score` int(11) NOT NULL DEFAULT 0,
  `final_score` int(11) NOT NULL DEFAULT 0,
  `rank_position` int(11) DEFAULT NULL,
  `credits_awarded` int(11) NOT NULL DEFAULT 0,
  `submitted_at` datetime NOT NULL DEFAULT current_timestamp(),
  `is_locked` tinyint(1) NOT NULL DEFAULT 1,

  PRIMARY KEY (`id`),

  UNIQUE KEY `uq_tournament_user` (`tournament_id`,`user_id`),

  KEY `idx_tournament_id` (`tournament_id`),
  KEY `idx_user_id` (`user_id`),

  CONSTRAINT `fk_entry_tournament`
    FOREIGN KEY (`tournament_id`) REFERENCES `tournaments` (`id`)
    ON DELETE CASCADE,

  CONSTRAINT `fk_entry_user`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;


CREATE TABLE tournaments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    start_at DATETIME NOT NULL,
    entry_deadline DATETIME NOT NULL,
    result_at DATETIME NOT NULL,
    status ENUM('upcoming','open','closed','finished') NOT NULL DEFAULT 'upcoming',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);