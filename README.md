# Security Lab Web App

A hands-on learning project: building a small web application from scratch (HTML → CSS → JS → PHP → MySQL), then intentionally testing it for vulnerabilities and fixing them — to understand web security by building and breaking a real system.

## Stack
- Ubuntu (native, no VM)
- Apache2
- PHP 8 + MySQLi
- MySQL 8.0

## Progress

### ✅ Step 1: Web server setup
- Installed LAMP stack (`apache2`, `mysql-server`, `php`, `libapache2-mod-php`, `php-mysql`)
- Apache running and enabled on boot
- Web root ownership fixed (`/var/www/html`) for non-sudo editing

### ✅ Step 2: HTML page
- Basic `index.html` created

### ✅ Step 3: CSS styling
- `style.css` linked to `index.html`

### ✅ Step 4: JavaScript
- `script.js` — simple DOM manipulation on button click

### ✅ Step 5-6: Database + connection
- MySQL database `webapp` created
- `users` table: `id`, `username`, `password`, `email`
- Dedicated least-privilege MySQL user `webapp_user` (not using root in app code)
- `db.php` — reusable MySQLi connection file
- Connection verified via `test-connection.php`

### 🔄 In progress: Real authentication system
- ✅ `register.html` + `register.php` — registration with `password_hash()` (bcrypt), prepared statements (SQL-injection-safe by design)
- ⬜ `login.php` — login with PHP sessions
- ⬜ `dashboard.php` — protected page, only accessible when logged in
- ⬜ `logout.php` — session destruction

## Security principles applied so far
- **Prepared statements** (`?` placeholders + `bind_param`) instead of raw string concatenation — prevents SQL injection
- **Password hashing** via `password_hash()` (bcrypt + automatic salting) — never storing plain-text passwords
- **Least-privilege database user** — app connects as `webapp_user` with access scoped only to the `webapp` database, not `root`
- **Output escaping** via `htmlspecialchars()` — prevents reflected XSS when echoing user input back to the page

## Next steps (planned)
1. Finish login/session/dashboard/logout flow
2. **Step 7: APIs** — experiment with a simple API endpoint (e.g., returning JSON user data)
3. **Step 8: Vulnerability testing** — deliberately review/introduce common flaws (SQLi, XSS, broken auth) to understand them hands-on, in this isolated local environment only
4. **Step 9: Remediation** — fix each vulnerability found in Step 8 and document the before/after

## Notes
- This app is intentionally built and tested **locally only** — not deployed to the public internet — since Step 8 involves deliberately probing for security weaknesses.
- Database credentials in `db.php` are placeholders for local development only.
