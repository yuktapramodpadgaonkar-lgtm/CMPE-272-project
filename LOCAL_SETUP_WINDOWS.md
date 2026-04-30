# Local setup on Windows (XAMPP + optional standalone PHP)

## Your folders

| Folder | Role |
|--------|------|
| **`C:\Users\018464615\php`** | Standalone PHP — use for `php -S` in a terminal **or** after adding to **PATH** |
| **`C:\xampp`** | **Apache + MySQL + PHP** — use this to open the site in a **browser** at `http://localhost/...` |

For the CMPE 272 site, **XAMPP is the main setup**. The folder `C:\Users\018464615\php` is optional (CLI / built-in server).

---

## A. Automated copy into XAMPP (recommended)

1. Install **XAMPP** (default `C:\xampp`).
2. In **XAMPP Control Panel**, start **Apache** and **MySQL**.
3. Open **PowerShell** and run:

```powershell
cd "C:\Users\018464615\Downloads\Sem2\CMPE-272\CMPE-272-project\scripts"
powershell -ExecutionPolicy Bypass -File .\setup-local-xampp.ps1
```

This copies the project to **`C:\xampp\htdocs\CMPE-272-project`** and creates **`cookie-business\includes\db_config.php`** with `root` and **empty password** (XAMPP default).

4. **Import the database** in phpMyAdmin: **Import** → select  
   `C:\xampp\htdocs\CMPE-272-project\cookie-business\sql\cmpe272_company_users.sql` → Go.

5. Open:

- http://localhost/CMPE-272-project/cookie-business/
- http://localhost/CMPE-272-project/cookie-business/api_users.php

---

## B. Standalone PHP (`C:\Users\018464615\php`)

**I cannot change your Windows PATH from here.** You do this once:

1. Search **“environment variables”** → **Edit environment variables for your account**.
2. **Path** → **Edit** → **New** → `C:\Users\018464615\php` → OK.
3. New terminal: `php -v`.

**php.ini (optional):**

```text
cd C:\Users\018464615\php
copy php.ini-development php.ini
```

Edit `php.ini`: uncomment `extension_dir` and `;extension=curl` → `extension=curl` if you use `php -S` and need cURL.

**Built-in server (no XAMPP Apache):**

```powershell
cd C:\xampp\htdocs\CMPE-272-project\cookie-business
# Still need MySQL running (XAMPP MySQL is fine)
C:\Users\018464615\php\php.exe -S localhost:8080
```

Browser: http://localhost:8080/ — but MySQL must be running and `db_config.php` must match (`127.0.0.1`, `root`, etc.).

---

## If phpMyAdmin says “connection refused”

**Start MySQL** in XAMPP Control Panel (green). Error **2002** = MySQL not running.

---

## Summary

- **Browser testing:** XAMPP + run **`setup-local-xampp.ps1`** + import SQL + open `http://localhost/CMPE-272-project/cookie-business/`.
- **`C:\Users\018464615\php`:** add to **PATH** if you want `php` in the terminal; not required if you only use XAMPP.
