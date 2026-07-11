# Security Lab Web App

A hands-on learning project: building a small web application from scratch (HTML → CSS → JS → PHP → MySQL), then intentionally testing it for vulnerabilities and fixing them — to understand web security by building and breaking a real system.

## Stack
- Ubuntu (native, no VM)
- Apache2
- PHP 8 + MySQLi
- MySQL 8.0

## Progress

### ✅ Phase 1: Core Application (Complete)

**Step 1: Web server setup**
- Installed LAMP stack (`apache2`, `mysql-server`, `php`, `libapache2-mod-php`, `php-mysql`)
- Apache running and enabled on boot
- Web root ownership fixed (`/var/www/html`) for non-sudo editing

**Step 2-4: Frontend basics**
- `index.html` — basic page structure
- `style.css` — shared styling across all pages
- `script.js` — simple DOM manipulation example

**Step 5-6: Database + connection**
- MySQL database `webapp` created
- `users` table: `id`, `username`, `password`, `email`
- Dedicated least-privilege MySQL user `webapp_user` (not using root in app code)
- `db.php` — reusable MySQLi connection file

**Authentication system (complete):**
- `register.html` / `register.php` — user registration with `password_hash()` (bcrypt)
- `login.html` / `login.php` — login with `password_verify()` and PHP sessions
- `dashboard.php` — protected page, redirects unauthenticated visitors to login
- `logout.php` — destroys session on logout

### ⬜ Phase 2: Vulnerable Branch (Next)
- Reintroduce common vulnerabilities on a separate `vulnerable` git branch: SQL injection, stored XSS, IDOR, broken access control
- Document each vulnerability: description, proof of concept, impact

### ⬜ Phase 3: Attack & Verify
- Use Burp Suite and sqlmap against the local vulnerable branch
- Document exploitation with logs/screenshots

### ⬜ Phase 4: Remediate & Automate
- Fix each vulnerability on `main`, with before/after documentation
- Build a small Python static-analysis script to flag dangerous PHP patterns (raw SQL concatenation, unescaped output)

## Security principles applied (Phase 1)
- **Prepared statements** (`?` placeholders + `bind_param`) — prevents SQL injection
- **Password hashing** via `password_hash()` (bcrypt + automatic salting) — never storing plain-text passwords
- **Session-based authentication** — `session_start()`, `$_SESSION`, proper `session_destroy()` on logout
- **Least-privilege database user** — app connects as `webapp_user` scoped only to the `webapp` database, not `root`
- **Output escaping** via `htmlspecialchars()` — prevents reflected/stored XSS when echoing user input

## Architecture
