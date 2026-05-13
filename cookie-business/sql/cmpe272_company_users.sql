-- CMPE 272: company users database (directory + combined-users lab)
-- Run as MySQL root (or a user that can CREATE DATABASE).

CREATE DATABASE IF NOT EXISTS cmpe272_company_users
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE cmpe272_company_users;

-- Replace old `users` shape (e.g. from an earlier lab) so CREATE + INSERT always match.
DROP TABLE IF EXISTS users;

CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(100) NOT NULL,
  last_name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL,
  home_address VARCHAR(255) NOT NULL,
  home_phone VARCHAR(40) NOT NULL DEFAULT '',
  cell_phone VARCHAR(40) NOT NULL DEFAULT '',
  -- Kept in sync with first + last name for JSON API / combined list
  name VARCHAR(202) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- At least 20 Sweet Crumb directory users
INSERT INTO users (first_name, last_name, email, home_address, home_phone, cell_phone, name) VALUES
('Yukta','Padgaonkar','yuktapramod.padgaonkar@sjsu.edu','1 Cookie Row, San Jose, CA 95112','408-555-1001','408-555-1002','Yukta Padgaonkar'),
('Mary','Smith','mary@sweetcrumb.test','22 Baker St, San Jose, CA','408-555-1003','408-555-1004','Mary Smith'),
('John','Wang','john@sweetcrumb.test','305 Chip Way, Santa Clara, CA','408-555-1005','408-555-1006','John Wang'),
('Alex','Bington','alex@sweetcrumb.test','48 Sugar Hill Rd, Sunnyvale, CA','408-555-1007','408-555-1008','Alex Bington'),
('Oliver','Crumb','oliver.crumb@sweetcrumb.test','12 Maple Rd, San Jose, CA','408-555-0101','408-555-0102','Oliver Crumb'),
('Nina','Icing','nina.icing@sweetcrumb.test','44 Butter Ln, San Jose, CA','408-555-0103','408-555-0104','Nina Icing'),
('Ethan','Glaze','ethan.glaze@sweetcrumb.test','9 Sugar St, Santa Clara, CA','408-555-0105','408-555-0106','Ethan Glaze'),
('Sophia','Dough','sophia.dough@sweetcrumb.test','221 Chip Ave, San Jose, CA','408-555-0107','408-555-0108','Sophia Dough'),
('Liam','Batter','liam.batter@sweetcrumb.test','301 Oven Way, Fremont, CA','510-555-0201','510-555-0202','Liam Batter'),
('Maya','Sugar','maya.sugar@sweetcrumb.test','87 Cocoa Dr, Palo Alto, CA','650-555-0301','650-555-0302','Maya Sugar'),
('Noah','Spice','noah.spice@sweetcrumb.test','15 Vanilla Ct, Sunnyvale, CA','408-555-0209','408-555-0210','Noah Spice'),
('Chloe','Crumble','chloe.crumble@sweetcrumb.test','60 Frosting Blvd, San Jose, CA','408-555-0211','408-555-0212','Chloe Crumble'),
('Lucas','Muffin','lucas.muffin@sweetcrumb.test','100 Cookie Jar Rd, Saratoga, CA','408-555-0213','408-555-0214','Lucas Muffin'),
('Emma','Chips','emma.chips@sweetcrumb.test','222 Chocolate Row, Oakland, CA','510-555-0401','510-555-0402','Emma Chips'),
('James','Honey','james.honey@sweetcrumb.test','410 Golden Way, Fremont, CA','510-555-0403','510-555-0404','James Honey'),
('Ava','Molasses','ava.molasses@sweetcrumb.test','505 Brownstone Pl, Palo Alto, CA','650-555-0501','650-555-0502','Ava Molasses'),
('Henry','Shortbread','henry.shortbread@sweetcrumb.test','707 Crisp Ct, Redwood City, CA','650-555-0503','650-555-0504','Henry Shortbread'),
('Isabella','Frost','isabella.frost@sweetcrumb.test','908 Sprinkle St, Sunnyvale, CA','408-555-0601','408-555-0602','Isabella Frost'),
('Benjamin','Drizzle','benjamin.drizzle@sweetcrumb.test','12 Glaze Terrace, Berkeley, CA','510-555-0603','510-555-0604','Benjamin Drizzle'),
('Ruby','Sesame','ruby.sesame@sweetcrumb.test','630 Crunch Path, Campbell, CA','408-555-0701','408-555-0702','Ruby Sesame');

-- Local mirror of marketplace-authenticated customers (SSO).
CREATE TABLE IF NOT EXISTS site_users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  marketplace_user_id INT UNSIGNED NOT NULL UNIQUE,
  username VARCHAR(50) NOT NULL,
  full_name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  last_logged_in DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Customer accounts (OurMarketplace-style: username, email, full_name, password_hash)
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

-- Dedicated MySQL user (optional — adjust password and run as root):
-- CREATE USER IF NOT EXISTS 'cmpe272_user'@'localhost' IDENTIFIED BY 'CHOOSE_A_STRONG_PASSWORD';
-- GRANT ALL PRIVILEGES ON cmpe272_company_users.* TO 'cmpe272_user'@'localhost';
-- FLUSH PRIVILEGES;
