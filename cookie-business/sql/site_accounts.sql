-- Sweet Crumb: customer accounts (same shape as OurMarketplace users: username, email, full_name, password_hash).
-- Run in database cmpe272_company_users (phpMyAdmin: select DB → Import this file).

USE cmpe272_company_users;

CREATE TABLE IF NOT EXISTS site_accounts (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL,
  email VARCHAR(100) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  full_name VARCHAR(100) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_site_accounts_username (username),
  UNIQUE KEY uq_site_accounts_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
