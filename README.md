# In Loving Memory — Sunday Makatarehi Bello

A tribute/memorial website built in plain PHP + MySQL (no framework). Visitors can read the biography and life timeline, browse a photo/video gallery, and leave tributes (with an optional photo, video, or YouTube link). An admin panel moderates submissions before they go live.

## Stack

- PHP 8+ (no Composer, no framework — just plain scripts and a couple of shared includes)
- MySQL / MariaDB
- No JS build step — `script.js` and `style.css` are loaded directly

## Project structure

```
index.php              Home page (summary): biography, timeline, family tree, service info, teasers
gallery.php             Full photo/video gallery (masonry layout, lightbox)
tributes.php             Full "Wall of Remembrance" + tribute submission form
tribute.php               Single tribute detail page (?id=N)
submit_tribute.php         Handles tribute form submissions (POST target)
db.php                      PDO connection helper
config.php                    Real config — NOT in git, you create this locally (see Setup)
config.example.php               Template for config.php — safe to commit, no real secrets
schema.sql                         Database table definitions
includes/
  header.php                        Shared <head>, hero section, sticky nav
  footer.php                        Shared service section, lightbox markup, closing tags
  functions.php                     Shared PHP helpers (escaping, rendering, relationship list)
  upload.php                        File upload validation/handling
  youtube.php                        YouTube URL → video ID extraction
admin/
  login.php, logout.php, auth_check.php    Session-based admin auth
  dashboard.php                              Moderation queue + delete controls
  update_status.php                           Approve/reject handler
  delete.php                                   Permanently deletes a tribute or media row (+ its file)
  upload_media.php                              Admin direct-to-gallery upload (file or YouTube link)
images/                 Static site images (banner, portrait)
uploads/photos/, uploads/videos/   User-uploaded media (gitignored — real content lives only on each server)
assets/                Downloadable files (e.g. program.pdf)
```

---

## Local setup (Laragon)

1. Place the project in `laragon/www/SundayBello` (or wherever your vhost root is).
2. **Create your config file** — copy the template and fill in local values:
   ```
   # Windows (PowerShell or cmd)
   copy config.example.php config.php

   # macOS/Linux
   cp config.example.php config.php
   ```
   Defaults in the template (`DB_HOST=localhost`, `DB_USER=root`, `DB_PASS=''`) already match Laragon's default MySQL, so you mainly need to set `ADMIN_USERNAME` and generate `ADMIN_PASSWORD_HASH`:
   ```
   php -r "echo password_hash('yourpassword', PASSWORD_DEFAULT);"
   ```
   Paste the output into `ADMIN_PASSWORD_HASH` in `config.php`.
3. **Create the database** — in Laragon's HeidiSQL/phpMyAdmin, create a database named `sunday` (or whatever you set `DB_NAME` to), then import `schema.sql`.
4. Start Laragon and visit `http://sundaybello.test/` (or `http://localhost/SundayBello/`).
5. Log in to the admin panel at `/admin/` with the username/password you set in step 2.

---

## Deploying to Hostinger

### 1. Database

1. In hPanel, go to **Databases → MySQL Databases** and create a new database. Note the DB name, username, password, and host it gives you (usually `localhost` on shared hosting, but hPanel's database page is the source of truth if it shows something else).
2. Open **phpMyAdmin** for that database → **Import** tab → upload `schema.sql` to create the tables.
   - If you want to bring existing content from another environment, export it there first (`mysqldump -u root your_db > export.sql`, or via phpMyAdmin's Export tab) and import that instead of/after `schema.sql`. Don't commit that export file to git — see **Security** below.

### 2. Files

Upload everything into `public_html` (or your subdomain's folder) via hPanel's File Manager or FTP/SFTP, **including**:
- All `.php`, `.css`, `.js` files
- `images/`, `assets/`
- `uploads/photos/` and `uploads/videos/` — if migrating existing content, bring the actual files across; the `media` table's `file_path` column references these exact filenames, so missing files mean broken images.
- `.htaccess` (see **Security** — this file is doing real work, don't skip it)

**Do not upload `config.php`.** Instead, create a fresh one directly on the server (via File Manager's "New File" or by copying `config.example.php` to `config.php` there) with your **production** database credentials from step 1, plus your own `ADMIN_USERNAME`/`ADMIN_PASSWORD_HASH`.

### 3. PHP version

In hPanel → **Advanced → PHP Configuration**, pick PHP 8.0 or newer.

### 4. Go live checklist

- [ ] Visit the site — home page, Gallery, Tributes all load
- [ ] Log into `/admin/` with your production credentials
- [ ] Submit a test tribute (with a photo) to confirm the server can write to `uploads/`
- [ ] Approve it from the dashboard, confirm it appears on the public site
- [ ] Delete the test tribute from the dashboard when done
- [ ] Enable Hostinger's free SSL certificate (hPanel → SSL) so the admin login isn't sent over plain HTTP

---

## Security notes

- **`config.php` is gitignored on purpose.** It holds real DB and admin credentials. Never commit it — copy `config.example.php` instead, on every environment (local, Hostinger, etc.) independently.
- **`.htaccess` at the project root** disables directory listing (so browsing to `/admin/` or `/includes/` can't expose file names) and explicitly blocks direct web access to `config.php`, any `*.sql` file, and stray backup files (`*.bak`, `*~`). Confirm your host honors `.htaccess` (Hostinger does by default) after deploying.
- **`admin/index.php`** redirects `/admin/` straight to the login flow, so there's no bare directory listing at that URL either.
- **Real user content is gitignored**: `uploads/photos/*`, `uploads/videos/*`, and any `*.sql` dump (except `schema.sql`, which has no data) never get committed. If you need to move real data between environments, transfer the dump/files directly (SFTP, phpMyAdmin export/import) — don't put them in git, even temporarily.
- **`uploads/.htaccess`** prevents any uploaded file from ever executing as a script, even if someone found a way to upload something malicious disguised as an image.

---

## Admin panel

`/admin/dashboard.php` (after logging in) shows:
- **Pending Tributes / Pending Media** — Approve, Reject, or Delete. Approving or rejecting a tribute cascades the same decision to any photo/video/YouTube link attached to it.
- **Published Tributes / Published Media** — already-live content, with a Delete option (permanently removes the DB row and, for uploaded files, the file itself).
- **+ Upload Photo/Video** (`upload_media.php`) — publish a photo, video, or YouTube link directly to the Gallery, bypassing the moderation queue (since it's you adding it).

## Notes for future work

- `TRIBUTE_RELATIONSHIPS` (the dropdown list on the tribute form) is defined once in `includes/functions.php` — edit that array to change the options everywhere at once.
- The lightbox, gallery masonry layout, and tribute "flow" cards are shared components (`render_gallery_item()` and `render_tribute_flow_card()` in `includes/functions.php`) reused across the home page, Gallery, and tribute detail page — change them once, they update everywhere.
