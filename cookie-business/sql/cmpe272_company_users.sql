-- CMPE 272 Lab: Combined list of users (Company A / B / C each run this on THEIR server)
-- Run as MySQL root (or a user that can CREATE DATABASE).

CREATE DATABASE IF NOT EXISTS cmpe272_company_users
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE cmpe272_company_users;

CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  email VARCHAR(150) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample users for Sweet Crumb (Company A) — change names for your real company
INSERT INTO users (name, email) VALUES
('Yukta Padgaonkar', 'yuktapramod.padgaonkar@sjsu.edu'),
('Mary Smith', 'mary@sweetcrumb.test'),
('John Wang', 'john@sweetcrumb.test'),
('Alex Bington', 'alex@sweetcrumb.test');

-- Grant for a dedicated MySQL user (optional — adjust password and run as root):
-- CREATE USER IF NOT EXISTS 'cmpe272_user'@'localhost' IDENTIFIED BY 'CHOOSE_A_STRONG_PASSWORD';
-- GRANT ALL PRIVILEGES ON cmpe272_company_users.* TO 'cmpe272_user'@'localhost';
-- FLUSH PRIVILEGES;
