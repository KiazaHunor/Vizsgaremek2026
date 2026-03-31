CREATE TABLE card_game_results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    result ENUM('win', 'draw', 'loss') NOT NULL,
    player_score INT NOT NULL DEFAULT 0,
    enemy_score INT NOT NULL DEFAULT 0,
    played_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);