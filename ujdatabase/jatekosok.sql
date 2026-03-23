CREATE DATABASE IF NOT EXISTS fizzliga_database
CHARACTER SET utf8mb4
COLLATE utf8mb4_hungarian_ci;

USE fizzliga_database;

CREATE TABLE players (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    teams VARCHAR(255) NOT NULL,
    positions VARCHAR(100) NOT NULL,
    nationalities VARCHAR(100) NOT NULL,
    UNIQUE (name, teams, nationalities, positions)
);