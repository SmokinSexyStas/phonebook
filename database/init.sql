CREATE DATABASE IF NOT EXISTS phonebook
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE phonebook;

CREATE TABLE users (
                       id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                       login VARCHAR(16) NOT NULL UNIQUE,
                       email VARCHAR(255) NOT NULL UNIQUE,
                       password VARCHAR(255) NOT NULL,
                       remember_token VARCHAR(100) NULL,
                       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                       updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

                       INDEX idx_login (login),
                       INDEX idx_email (email),
                       INDEX idx_remember_token (remember_token)
);

CREATE TABLE contacts (
                          id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                          user_id INT UNSIGNED NOT NULL,
                          first_name VARCHAR(100) NOT NULL,
                          last_name VARCHAR(100) NOT NULL,
                          phone VARCHAR(20) NOT NULL,
                          email VARCHAR(255) NOT NULL,
                          image_path VARCHAR(500) NULL,
                          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                          updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

                          FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                          INDEX idx_user_id (user_id),
                          INDEX idx_name (first_name, last_name)
);

CREATE TABLE user_sessions (
                               id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                               user_id INT UNSIGNED NOT NULL,
                               token VARCHAR(100) NOT NULL UNIQUE,
                               expires_at DATETIME NOT NULL,
                               created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

                               FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                               INDEX idx_token (token),
                               INDEX idx_user_id (user_id),
                               INDEX idx_expires_at (expires_at)
);