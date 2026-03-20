# CMPE 272 Project

Basic webpage for course submission, hosted on GitHub Pages.

## View the site

After enabling GitHub Pages, the site will be available at:

**https://YOUR_USERNAME.github.io/CMPE-272-project/**

Replace `YOUR_USERNAME` with your GitHub username.

## PHP site (`cookie-business/`)

Upload the `cookie-business` folder to your PHP host (e.g. under `CMPE-272-project/cookie-business/`).

### If the site shows a blank page or 500 error

1. **Re-upload** `includes/cookies_track.php` — older PHP versions crash on `setcookie(..., [array])`; the current file uses a PHP 7.1–compatible `setcookie` form.
2. In hosting control panel, set **PHP 7.4+** (or 8.x) if you can.
3. Turn on **error logging** or set `display_errors` temporarily to see the real error message.
4. Open **`products.php`** directly (not only the folder URL):  
   `https://yourdomain.com/.../cookie-business/products.php`
