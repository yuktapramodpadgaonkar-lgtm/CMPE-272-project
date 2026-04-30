# CMPE 272 — Lightsail (Bitnami LAMP) setup — step by step

Use this after your domain points to the instance (or use the Lightsail static IP in the URL).

---

## Part 0 — Before you SSH

1. In **AWS Lightsail**: note your instance **public IP** (or attach a **static IP**).
2. **Networking** tab: allow **HTTP (80)** and **HTTPS (443)** in the firewall.
3. Download your **SSH key** (`.pem`) from Lightsail if you use it, or use **Connect using SSH** in the browser.

---

## Part 1 — Connect to the server

**Option A — Browser:** Lightsail console → your instance → **Connect using SSH**

**Option B — Your PC (PowerShell), replace path and IP:**

```powershell
ssh -i "C:\path\to\your-key.pem" bitnami@YOUR_STATIC_IP
```

You should see a prompt like: `bitnami@ip-...:~$`

---

## Part 2 — Get the code from GitHub

### 2a — If the repo is NOT on the server yet (first time)

```bash
cd /opt/bitnami/apache/htdocs
sudo rm -rf CMPE-272-project
sudo git clone https://github.com/yuktapramodpadgaonkar-lgtm/CMPE-272-project.git
sudo chown -R bitnami:daemon CMPE-272-project
```

*(Change the GitHub URL if your repo is different.)*

### 2b — If the repo already exists (update code)

```bash
cd /opt/bitnami/apache/htdocs/CMPE-272-project
git status
git pull origin main
```

If `git pull` complains about local changes:

```bash
cd /opt/bitnami/apache/htdocs/CMPE-272-project
git fetch origin
git reset --hard origin/main
git clean -fd
```

---

## Part 3 — MySQL: create database and users table

### 3a — Find MySQL root password (Bitnami)

```bash
cat ~/bitnami_application_password
```

Copy the password (you will paste it when `mysql -p` asks).

### 3b — Import the SQL file

```bash
cd /opt/bitnami/apache/htdocs/CMPE-272-project/cookie-business
mysql -u root -p < sql/cmpe272_company_users.sql
```

Enter the password from step 3a when prompted.

**If `mysql` command not found**, try:

```bash
/opt/bitnami/mariadb/bin/mysql -u root -p < /opt/bitnami/apache/htdocs/CMPE-272-project/cookie-business/sql/cmpe272_company_users.sql
```

### 3c — (Recommended) Create a dedicated DB user

```bash
mysql -u root -p
```

In the MySQL prompt:

```sql
CREATE USER IF NOT EXISTS 'cmpe272_user'@'localhost' IDENTIFIED BY 'ChooseOneStrongPassword123!';
GRANT ALL PRIVILEGES ON cmpe272_company_users.* TO 'cmpe272_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

Remember **`ChooseOneStrongPassword123!`** — you will put it in `db_config.php`.

---

## Part 4 — PHP database config (`db_config.php`)

```bash
cd /opt/bitnami/apache/htdocs/CMPE-272-project/cookie-business/includes
cp db_config.example.php db_config.php
nano db_config.php
```

Edit these values (use your real password if you created `cmpe272_user`):

| Key | Typical value |
|-----|----------------|
| `host` | `127.0.0.1` |
| `user` | `cmpe272_user` (or `root` if you skip 3c) |
| `pass` | the password you set |
| `dbname` | `cmpe272_company_users` |
| `company_name` | your company name |
| `company_code` | `A`, `B`, or `C` (agree with your group) |
| `remote_apis` | full **https** URLs to teammates’ `api_users.php` |

Save in **nano**: `Ctrl+O`, Enter, then `Ctrl+X`.

---

## Part 5 — PHP cURL (required for combined-users lab)

```bash
php -m | grep -i curl
```

If you see **`curl`**, you are done.

If **empty**, enable it in Bitnami’s `php.ini` (path may vary):

```bash
sudo grep -n "extension=curl" /opt/bitnami/php/etc/php.ini
```

Uncomment `extension=curl` if needed, then:

```bash
sudo /opt/bitnami/ctlscript.sh restart apache
```

(Bitnami docs: search “bitnami enable php curl” if your image differs.)

---

## Part 6 — File permissions (if you get 403 / permission errors)

```bash
sudo chown -R bitnami:daemon /opt/bitnami/apache/htdocs/CMPE-272-project
find /opt/bitnami/apache/htdocs/CMPE-272-project -type d -exec chmod 755 {} \;
find /opt/bitnami/apache/htdocs/CMPE-272-project -type f -exec chmod 644 {} \;
```

---

## Part 7 — Test in the browser

Replace **`YOUR_DOMAIN`** with your real domain or `http://YOUR_STATIC_IP`.

| What | URL |
|------|-----|
| Cookie business home | `https://YOUR_DOMAIN/CMPE-272-project/cookie-business/` |
| Users JSON API | `https://YOUR_DOMAIN/CMPE-272-project/cookie-business/api_users.php` |
| Combined users (cURL lab) | `https://YOUR_DOMAIN/CMPE-272-project/cookie-business/combined_users.php` |

If you use **only IP** and no SSL:

`http://YOUR_IP/CMPE-272-project/cookie-business/`

---

## Part 8 — HTTPS (optional but good for class)

Bitnami often provides **`bncert-tool`** or Let’s Encrypt instructions. Run on the server:

```bash
sudo /opt/bitnami/bncert-tool
```

Follow prompts for your domain.

---

## Part 9 — Group project (combined users)

1. Each teammate deploys the same code + their own DB + their own `db_config.php`.
2. Each person’s **`api_users.php`** must be reachable from the internet (test in an incognito window).
3. Swap **full HTTPS URLs** and paste them into each other’s **`remote_apis`** in `db_config.php`.

---

## Quick checklist

- [ ] Firewall: 80 / 443 open  
- [ ] Code in `/opt/bitnami/apache/htdocs/CMPE-272-project` (`git pull`)  
- [ ] SQL imported: `cmpe272_company_users` + `users` table  
- [ ] `cookie-business/includes/db_config.php` exists and is correct  
- [ ] `php -m` shows `curl`  
- [ ] Browser: `api_users.php` returns JSON  
- [ ] Browser: `combined_users.php` shows local + remote tables  

---

## Troubleshooting

| Problem | What to try |
|---------|-------------|
| 404 | Path wrong — confirm files under `htdocs/CMPE-272-project/cookie-business/` |
| 500 / blank | `sudo tail -50 /opt/bitnami/apache/logs/error_log` |
| DB connection error | `db_config.php` user/pass/host/dbname; MySQL running: `sudo /opt/bitnami/ctlscript.sh status` |
| Remote section fails | Teammate URL must be **https** (or http if you all agree), reachable from your server |

---

*Repo default remote in examples: `yuktapramodpadgaonkar-lgtm/CMPE-272-project` — change if yours differs.*
