CREATE DATABASE IF NOT EXISTS user_auth;
USE user_auth;

DROP TABLE IF EXISTS users;
CREATE TABLE users (
  id int(11) NOT NULL AUTO_INCREMENT,
  username varchar(50) NOT NULL,
  password varchar(255) NOT NULL,
  email varchar(100) NOT NULL,
  token varchar(64) DEFAULT NULL,
  token_expiry datetime DEFAULT NULL,
  password_reset_token varchar(64) DEFAULT NULL,
  password_reset_expiry datetime DEFAULT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  email_token varchar(64) DEFAULT NULL,
  email_verified tinyint(1) NOT NULL DEFAULT 0,
  current_streak int NOT NULL DEFAULT 0,
  best_streak int NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  UNIQUE KEY uq_users_username (username),
  UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE users ADD profile_image VARCHAR(255) NULL;
CREATE TABLE questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    text VARCHAR(500) NOT NULL,
    answer_a VARCHAR(255) NOT NULL,
    answer_b VARCHAR(255) NOT NULL,
    answer_c VARCHAR(255) NOT NULL,
    answer_d VARCHAR(255) NOT NULL,
    correct_answer VARCHAR(255) NOT NULL,
    active_date DATE NULL
);
CREATE TABLE user_answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    question_id INT NOT NULL,
    selected_answer VARCHAR(255) NOT NULL,
    answer_date DATE NOT NULL,
    is_correct TINYINT(1) NOT NULL DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (question_id) REFERENCES questions(id)
);
