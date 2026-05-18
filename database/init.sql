-- IVWA Database Schema
-- Database untuk Intentional Vulnerable Web Application

-- Create Database
CREATE DATABASE IF NOT EXISTS `ivwa`;
USE `ivwa`;

-- Table: users
-- Menyimpan data user login
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `email` VARCHAR(100),
  `full_name` VARCHAR(100),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: comments
-- Menyimpan comments/posts dari users
-- VULNERABILITY: Digunakan untuk demonstrasi Stored XSS
CREATE TABLE `comments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `username` VARCHAR(50) NOT NULL,
  `comment_text` LONGTEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert dummy data users
-- Password: password123 (plain text - VULNERABLE!)
INSERT INTO `users` (`username`, `password`, `email`, `full_name`) VALUES
('admin', 'password123', 'admin@ivwa.local', 'Administrator'),
('user1', 'password123', 'user1@ivwa.local', 'User One'),
('user2', 'password123', 'user2@ivwa.local', 'User Two'),
('hacker', 'password123', 'hacker@ivwa.local', 'Hacker');

-- Insert dummy comments untuk testing
INSERT INTO `comments` (`user_id`, `username`, `comment_text`) VALUES
(1, 'admin', 'Welcome to IVWA! This is an intentional vulnerable application for learning purposes.'),
(2, 'user1', 'This is a sample comment for testing the application functionality.'),
(3, 'user2', 'Please be careful when testing XSS vulnerabilities on this platform.');
