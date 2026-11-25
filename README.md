# Dream Spark — Cinematic Agency Website + PHP/MySQL Admin CMS

A premium digital-marketing agency website (Home, About, Our Brands, Contact) with a complete **Core PHP 8.2+ / MySQL 8** content-management system.
Every word, image, section, brand, service, testimonial, team member, contact detail and SEO tag lives in MySQL and is edited from `/admin` — no code changes needed. Press **Save** and the next page view shows the change.

**Stack:** HTML5 · CSS3 · Vanilla JS (ES6) · Bootstrap 5 grid · Font Awesome · Google Fonts · PHP 8.2+ (PDO, sessions) · MySQL 8 / MariaDB 10.4+ · Apache.
No Firebase, Laravel, React, Node or external CMS.

---

## 1. Features

**Public site** — animated preloader, fixed transparent navbar, cinematic hero with spark-particle canvas, custom cursor, magnetic buttons, word-by-word headline reveal, scroll reveals, parallax, image wipes, marquee, counters, testimonial slider, brand filters + detail modal, page transitions, scroll progress, back-to-top, WhatsApp button, `prefers-reduced-motion` support, fully responsive.

**Home**: hero · marquee · about · services · industries we serve · portfolio (brands) · why choose us · process · statistics · testimonials · CTA.
**About**: hero · introduction · story + timeline · mission & vision · values · expertise bars · why Dream Spark · process · team · CTA.
**Our Brands**: 5 industry brands (Jewellery, Restaurant & Cafe, Guest House & Hotel, Medical & Healthcare, Educational Institution) with logo, cover, description, services, website, socials, filter and modal.
**Contact**: details from Settings, AJAX form (name, email, phone, company, industry, service, subject, message) with CSRF, honeypot, signed timing check, per-IP rate limit, duplicate detection, server validation, Google map.

**Admin CMS** — Login · first-admin Setup · Dashboard (6 metrics, 12-month chart, recent enquiries, activity) · Pages & Sections (edit, show/hide and reorder every section; repeaters for lists; page SEO) · Brands · Services · Testimonials · Team (CRUD, search, sort, publish switch, drag-and-drop order, image upload) · Media Library (multi-upload, WebP conversion, folders, alt text, rename, replace-everywhere, delete with usage warning) · Enquiries (search, filters, status: New/Contacted/In Progress/Converted/Closed, internal notes, reply by email/WhatsApp, delete, CSV export) · Settings (logo, favicon, contact, WhatsApp, address, socials, footer, SEO, administrators) · My Profile (details, password) · Logout. Toasts, confirmation dialogs, notification bell, profile menu, unsaved-changes warnings.

## 2. Folder structure

```
dream-spark/
├── index.php · about.php · brands.php · contact.php     public pages (sections rendered from MySQL)
├── actions/contact-submit.php                          public contact-form endpoint
├── .htaccess                                           blocks private folders, security headers, caching
├── admin/
│   ├── login.php · setup.php · dashboard.php · pages.php · brands.php · services.php
│   ├── testimonials.php · team.php · media.php · enquiries.php · settings.php · profile.php · logout.php
│   ├── actions/   page-save · brand-save/delete · service-save/delete · testimonial-save/delete · team-save/delete
│   │              collection-order (publish + reorder) · media-upload/delete/update/list · enquiry-update/delete/export
│   │              settings-save · admin-user-save/delete · profile-save · notifications · _collection (shared)
│   ├── css/admin.css
│   └── js/admin.js · media.js
├── config/      config.php (app settings) · database.php (credentials)          ← web access denied
├── includes/    bootstrap · db · functions · auth · csrf · form · uploads · schema · defaults
│                header · navbar · footer · admin-layout · admin-collection · sections/*.php   ← web access denied
├── assets/      css/ (style, animations, responsive) · js/ (main, animations, contact) · images/ · uploads/ (PHP disabled)
├── database/dream_spark.sql                                                       ← web access denied
└── storage/logs/                                                                   ← web access denied
```

**How content is stored.** `pages` holds each page's SEO. `page_sections` holds one row per section with common columns (title, subtitle/eyebrow, content, image, button text/url, order, visibility) and a JSON `extra` column for section-specific fields (lists, second button, badge…). Every editable field is declared once in `includes/schema.php`; the admin forms, the validator and the templates all use it.

## 3. Install locally with XAMPP (Windows / macOS / Linux)

1. Install **XAMPP** (PHP 8.2+) from apachefriends.org. Open the XAMPP Control Panel and **Start Apache and MySQL**.
   *MAMP users:* start the servers; MySQL user/password are `root`/`root`, port `8889`.
2. Copy the `dream-spark` folder into `C:\xampp\htdocs\` (macOS: `/Applications/XAMPP/htdocs/`).
3. Open **http://localhost/phpmyadmin** → **New** → database name `dream_spark`, collation `utf8mb4_unicode_ci` → **Create**.
4. Select `dream_spark` → **Import** → choose `database/dream_spark.sql` → **Import**. You should see 12 tables.
5. Open `config/database.php` and check the credentials (XAMPP default: user `root`, empty password; MAMP: `root`/`root`, port `8889`).
6. Open `config/config.php`:
   - set `SETUP_KEY` to your own long secret phrase,
   - set `APP_SECRET` to any long random text,
   - leave `BASE_URL_OVERRIDE` empty (auto-detected).
7. Visit **http://localhost/dream-spark/** — the website should load.
8. Visit **http://localhost/dream-spark/admin/setup.php**, enter the `SETUP_KEY`, your name, email and a password (10+ characters, letters and numbers). This creates the **first administrator**. The page then locks itself permanently (you may delete `admin/setup.php`).
9. Log in at **http://localhost/dream-spark/admin/** with that email and password.

> There is **no default username or password**. The admin account is always created by you in step 8. Additional admins/editors are added in **Settings → Administrators**. Forgotten password: another administrator can reset it there (or create a new admin by temporarily emptying the `admins` table and running setup again).

## 4. Try the main features (test checklist)

- [ ] **Page editor** — Admin → Pages & Sections → Home → Hero banner → change the heading to `Transform Your Business With *Digital* Innovation.` → **Save section** → reload the homepage.
- [ ] Hide a section with its switch, move another with the arrows → the homepage follows.
- [ ] **Brands** — Add brand → upload a logo and cover → Save → it appears on Our Brands. Toggle it to Hidden → it disappears. Drag rows to reorder.
- [ ] **Services / Testimonials / Team** — add, edit, hide, delete.
- [ ] **Media** — upload JPG/PNG/WebP/SVG, edit alt text, Replace (every page using it updates), Delete.
- [ ] **Contact form** — submit a real enquiry → it appears instantly in Admin → Enquiries with a bell notification → change status, add notes → **Export CSV**.
- [ ] **Settings** — change the phone number → footer and contact page update.
- [ ] **Security** — open `/admin/dashboard.php` in a private window (redirects to login); try 6 wrong passwords (locked for 15 min); try to open `/config/database.php` (403).

## 5. Deploy on cPanel hosting

1. **Domains**: make sure the domain points to the hosting and **SSL (AutoSSL / Let's Encrypt)** is active.
2. **MySQL Databases**: create a database (e.g. `myacc_dreamspark`), a user with a strong password, and **add the user to the database with ALL PRIVILEGES**.
3. **phpMyAdmin** → select the new database → **Import** `database/dream_spark.sql`.
4. **File Manager**: upload the project zip into `public_html/` (or a subfolder) and **Extract**. Make sure hidden files (`.htaccess`) are included.
5. Edit `config/database.php` with the cPanel database name, user and password.
6. Edit `config/config.php`: `APP_ENV = 'production'`, new `SETUP_KEY` and `APP_SECRET`, and (recommended) `BASE_URL_OVERRIDE = 'https://www.yourdomain.com/'`.
7. **Select PHP Version** (MultiPHP Manager): PHP **8.2 or newer** with extensions `pdo_mysql`, `gd` (WebP), `fileinfo`, `mbstring`, `dom`.
8. **Permissions**: folders `755`, files `644`; `assets/uploads/` and `storage/logs/` must be writable by PHP (usually `755` is enough on cPanel; use `775` only if uploads fail).
9. Open `https://yourdomain.com/admin/setup.php` and create the first administrator, then delete `admin/setup.php`.
10. In `.htaccess`, uncomment the **force HTTPS** rule and the `Strict-Transport-Security` header once HTTPS works.
11. Production test: run the checklist in §4 on the live domain, then replace all placeholder content (§7).

## 6. Security summary

| Area | Implementation |
|---|---|
| SQL injection | PDO prepared statements everywhere (`includes/db.php`), real prepares (`EMULATE_PREPARES=false`), table/column names only from code |
| XSS | All output through `e()` (`htmlspecialchars`); rich headings only allow `*word*` → `<em>`; links filtered (`javascript:` etc. rejected); icon classes whitelisted |
| CSRF | Per-session token on every form and AJAX request; checked with `hash_equals`; destructive actions POST-only (405 otherwise) |
| Authentication | `password_hash`/`password_verify` with automatic rehash, generic error messages, timing-safe check for unknown emails, session ID regenerated on login and password change |
| Sessions | HttpOnly + SameSite=Lax (+ Secure on HTTPS) cookies, strict mode, browser fingerprint, 2-hour idle timeout, POST+CSRF logout |
| Brute force | 5 failed logins per email / 10 per IP per 15 minutes (`login_attempts` table); setup key guesses throttled too |
| Authorisation | Roles `admin` / `editor`; settings and accounts are admin-only; you can't delete/demote yourself or the last admin |
| Uploads | Extension whitelist, real MIME check (finfo), `getimagesize`, 5 MB / 40 MP limits, double-extension block, random names in `assets/uploads/YYYY/MM/`, raster images re-encoded with GD (strips payloads), SVG allow-list sanitiser, PHP execution disabled in uploads, path-traversal-safe deletes |
| Contact spam | CSRF, honeypot, signed minimum time (3 s), 3 per IP per hour, duplicate detection (24 h), server validation, max 3 links |
| Private files | `.htaccess` denies `config/`, `includes/`, `database/`, `storage/`, `*.sql`, `*.md`, dotfiles; every include also exits if requested directly |
| Errors | `APP_ENV=production` hides errors and logs to `storage/logs/` |

## 7. Before launch — replace placeholder content

Contact email/phone/WhatsApp/address (**Settings**), testimonials (only publish real ones, with the client's permission), team names and photos, statistics, the sample brand portfolio and the SVG artwork (upload real photography). Then fill in **SEO** for each page and the default SEO in Settings.

## 8. Customising

* **New editable field**: add it to the section's `fields` in `includes/schema.php`, then print it in `includes/sections/<template>.php` with `<?= e(sv($s, 'my_field')) ?>`. No database change needed (it is stored in the section's JSON).
* **Fallback content**: `includes/defaults.php` (also the source of the SQL seed). If a section row is missing, the site shows this default instead of breaking.
* **Upload limits**: `UPLOAD_MAX_BYTES` / `UPLOAD_MAX_DIMENSION` in `config/config.php` (also raise `upload_max_filesize` in PHP if needed).

## 9. Troubleshooting

| Problem | Fix |
|---|---|
| “Can't connect to the database” on the login page | MySQL not started, wrong credentials in `config/database.php`, or SQL not imported |
| Setup says “change SETUP_KEY first” | Edit `SETUP_KEY` in `config/config.php` (12+ characters) |
| Images won't upload | File > 5 MB, folder `assets/uploads` not writable, or `upload_max_filesize` too small in PHP settings |
| Uploaded photos stay JPG/PNG | The server's GD extension lacks WebP — images still work, just not converted |
| CSS/links broken in a subfolder | Set `BASE_URL_OVERRIDE` to the full URL ending with `/` |
| 500 error on cPanel | Some hosts disallow `php_value` in `.htaccess` — remove the `<IfModule mod_php.c>` block |
| “Session expired” toasts | You were idle for 2 hours or logged in elsewhere — refresh and log in again |
