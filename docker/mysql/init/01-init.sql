-- Initialize the attendance database
CREATE DATABASE IF NOT EXISTS attendance_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create user and grant privileges
CREATE USER IF NOT EXISTS 'attendance_user'@'%' IDENTIFIED BY 'attendance_password';
GRANT ALL PRIVILEGES ON attendance_db.* TO 'attendance_user'@'%';

-- Flush privileges
FLUSH PRIVILEGES;

-- Use the database
USE attendance_db;

-- Create any additional initialization here if needed
