CREATE DATABASE IF NOT EXISTS fizzliga_dbproba
CHARACTER SET utf8mb4
COLLATE utf8mb4_hungarian_ci;

USE fizzliga_dbproba;
CREATE TABLE players (
    id INT AUTO_INCREMENT PRIMARY KEY,
    team VARCHAR(100) NOT NULL,
    name VARCHAR(100) NOT NULL,
    position VARCHAR(50),
    nationality VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
