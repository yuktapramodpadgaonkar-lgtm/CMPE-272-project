# Combined users lab — setup on Lightsail / Bitnami LAMP

## 1. Upload / pull code

Ensure these exist on the server under `cookie-business/`:

- `sql/cmpe272_company_users.sql`
- `includes/db_config.example.php` → copy to `includes/db_config.php`
- `includes/db.php`
- `api_users.php`
- `combined_users.php`

## 2. Create MySQL database and table

SSH into the instance, then (Bitnami often puts MySQL here):

```bash
# Bitnami: get MySQL root password
cat ~/bitnami_application_password
# or check: cat ~/bitnami_credentials
```

Run SQL as root (password when prompted):

```bash
cd /opt/bitnami/apache/htdocs/CMPE-272-project/cookie-business
mysql -u root -p < sql/cmpe272_company_users.sql
```

If `mysql` is not in PATH:

```bash
/opt/bitnami/mariadb/bin/mysql -u root -p < sql/cmpe272_company_users.sql
```

## 3. Create MySQL user (recommended)

```bash
mysql -u root -p
```

```sql
CREATE USER IF NOT EXISTS 'cmpe272_user'@'localhost' IDENTIFIED BY 'YourStrongPasswordHere';
GRANT ALL PRIVILEGES ON cmpe272_company_users.* TO 'cmpe272_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

## 4. Configure PHP

```bash
cd /opt/bitnami/apache/htdocs/CMPE-272-project/cookie-business/includes
cp db_config.example.php db_config.php
nano db_config.php
```

Set:

- `host` → usually `127.0.0.1` or `localhost`
- `user` / `pass` → your MySQL user (e.g. `cmpe272_user` + password)
- `dbname` → `cmpe272_company_users`
- `company_name` / `company_code` → your company (e.g. `A` for Sweet Crumb)
- `remote_apis` → **full HTTPS URLs** to teammates’ `api_users.php`

Save: `Ctrl+O`, exit: `Ctrl+X`.

## 5. Enable PHP cURL (if needed)

```bash
php -m | grep -i curl
```

If empty, on Bitnami see their docs for enabling `curl` in `php.ini`, or install the curl extension for your stack.

## 6. Test in browser

- **API (JSON):**  
  `https://YOUR-DOMAIN/CMPE-272-project/cookie-business/api_users.php`
- **Combined page:**  
  `https://YOUR-DOMAIN/CMPE-272-project/cookie-business/combined_users.php`

## 7. Share with teammates

Send them your **exact** `api_users.php` URL. They put it in their `db_config.php` → `remote_apis`. You put theirs in yours.

## 8. Grading email

- Link: `combined_users.php` on your live site  
- Attach PHP: `api_users.php`, `combined_users.php`, `includes/db.php`, `includes/db_config.example.php` (as `.txt` if Gmail blocks `.php`)
