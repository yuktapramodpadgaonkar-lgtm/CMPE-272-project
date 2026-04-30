-- Run ONCE if you already have the old users table (id, name, email, created_at only).
USE cmpe272_company_users;

ALTER TABLE users
  ADD COLUMN first_name VARCHAR(100) NULL AFTER id,
  ADD COLUMN last_name VARCHAR(100) NULL AFTER first_name,
  ADD COLUMN home_address VARCHAR(255) NULL AFTER email,
  ADD COLUMN home_phone VARCHAR(40) NOT NULL DEFAULT '' AFTER home_address,
  ADD COLUMN cell_phone VARCHAR(40) NOT NULL DEFAULT '' AFTER home_phone;

-- Split legacy name into first/last when possible.
UPDATE users SET
  first_name = TRIM(SUBSTRING_INDEX(COALESCE(name, ''), ' ', 1)),
  last_name = CASE
    WHEN LOCATE(' ', COALESCE(name, '')) > 0
      THEN TRIM(SUBSTRING(name, LOCATE(' ', name)))
    ELSE ''
  END
WHERE first_name IS NULL;

UPDATE users SET last_name = '-' WHERE last_name IS NULL OR last_name = '';
UPDATE users SET home_address = 'Not provided' WHERE home_address IS NULL OR TRIM(home_address) = '';
UPDATE users SET home_phone = COALESCE(home_phone, '');
UPDATE users SET cell_phone = COALESCE(cell_phone, '');

ALTER TABLE users
  MODIFY first_name VARCHAR(100) NOT NULL,
  MODIFY last_name VARCHAR(100) NOT NULL,
  MODIFY home_address VARCHAR(255) NOT NULL;

-- Reach at least 20 rows (skipped if duplicates on email — adjust emails if needed).
INSERT IGNORE INTO users (first_name, last_name, email, home_address, home_phone, cell_phone, name) VALUES
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
