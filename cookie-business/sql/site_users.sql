-- Sweet Crumb: local mirror of marketplace-authenticated customers.
-- Import into cmpe272_company_users after pulling latest code.

USE cmpe272_company_users;

CREATE TABLE IF NOT EXISTS site_users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  marketplace_user_id INT UNSIGNED NOT NULL UNIQUE,
  username VARCHAR(50) NOT NULL,
  full_name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  last_logged_in DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
