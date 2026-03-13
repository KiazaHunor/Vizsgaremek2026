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
  PRIMARY KEY (id),
  UNIQUE KEY uq_users_username (username),
  UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
