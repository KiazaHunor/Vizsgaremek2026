CREATE DATABASE IF NOT EXISTS fizzliga_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_hungarian_ci;

USE fizzliga_db;
CREATE TABLE teams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) UNIQUE NOT NULL
);

CREATE TABLE nationalities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL
);

CREATE TABLE positions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL
);

CREATE TABLE players (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,

    team_id INT NOT NULL,
    nationality_id INT NOT NULL,
    position_id INT NOT NULL,

    FOREIGN KEY (team_id) REFERENCES teams(id),
    FOREIGN KEY (nationality_id) REFERENCES nationalities(id),
    FOREIGN KEY (position_id) REFERENCES positions(id)
);
