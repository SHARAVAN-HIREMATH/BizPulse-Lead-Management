# ⚡ BizPulse — Service & Lead Manager

> A production-ready mini CRM built with **PHP 8**, **PDO**, **MySQL**, **AJAX / Fetch API**, and **Tailwind CSS**.  
> Designed as an interview-ready portfolio project demonstrating professional full-stack PHP development.

[![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql&logoColor=white)](https://www.mysql.com/)
[![TailwindCSS](https://img.shields.io/badge/Tailwind-CDN-38BDF8?logo=tailwindcss&logoColor=white)](https://tailwindcss.com/)
[![License](https://img.shields.io/badge/License-Educational-green)](./README.md)

---

## 📸 Screenshots

| Landing Page | Admin Dashboard |
|---|---|
| ![Landing Page](https://placehold.co/600x350/4f46e5/ffffff?text=index.php+Landing+Page) | ![Admin Dashboard](https://placehold.co/600x350/0f172a/818cf8?text=admin.php+Dashboard) |

> _Replace the placeholders above with real screenshots after running the project locally._

---

## 🎯 Project Overview

BizPulse is a simple business website where:

- **Visitors** can browse services and submit an enquiry via a contact form.
- **Enquiries** are stored securely in a MySQL 8 database using PDO prepared statements.
- **Admins** can view all leads in a real-time dashboard and update their status **without reloading the page** (AJAX / Fetch API).

---

## ✨ Features

| Feature | Details |
|---|---|
| 🏠 Landing Page | Hero section, service cards, contact form |
| 📋 Lead Capture | Full Name, Email, Service, Message |
| ✅ Dual Validation | Client-side JavaScript + Server-side PHP |
| 💾 Database | MySQL 8 + PDO + Prepared Statements |
| 🔒 Security | Sanitised input, escaped output, no SQL injection |
| 📊 Admin Dashboard | Stats cards + responsive leads table |
| ⚡ AJAX Updates | Status change via Fetch API — no page reload |
| 🔍 Live Search | Filter leads by name, email, or service instantly |
| 📱 Responsive | Mobile card layout + desktop table layout |
| 🎨 Modern UI | Tailwind CSS, Inter font, glassmorphism, animations |

---

## 🛠 Technology Stack

| Layer | Technology |
|---|---|
| Frontend Markup | HTML5 (semantic) |
| Frontend Styling | Tailwind CSS (CDN) + Vanilla CSS |
| Frontend Logic | Vanilla JavaScript (ES2017+) |
| Backend | PHP 8.2 (no frameworks, no Composer) |
| Database | MySQL 8.0 |
| DB Abstraction | PDO with named prepared statements |
| Fonts | Google Fonts — Inter |

---

## 📁 Folder Structure

```
BizPulse/
│
├── index.php              # Public landing page + contact form
├── admin.php              # Internal admin dashboard
├── submit.php             # POST handler — validates & inserts lead into DB
├── update_status.php      # AJAX JSON endpoint — updates lead status
│
├── config/
│   └── database.php       # PDO singleton connection — edit credentials here
│
├── includes/
│   ├── header.php         # Shared nav + <head> (Tailwind CDN, Inter font)
│   └── footer.php         # Shared footer + mobile menu script
│
├── assets/
│   ├── css/
│   │   └── style.css      # Custom animations & micro-interactions
│   └── js/
│       └── admin.js       # Fetch API status updater + toast notifications
│
├── database/
│   ├── bizpulse.sql       # MySQL schema + optional seed data
│   ├── setup.php          # ⭐ One-command PHP setup script (recommended)
│   └── verify.php         # Full automated test suite (43 checks)
│
└── README.md              # This file
```

---

## ⚙️ Configuration — Read This First

### Step 1 — Edit Database Credentials

Open **`config/database.php`** and update these variables to match your environment:

```php
$host     = 'localhost'; // MySQL host — try '127.0.0.1' if localhost fails
$database = 'bizpulse'; // Database name — must match the SQL script
$username = 'root';     // Your MySQL username
$password = 'root';     // Your MySQL password — change this!
```

> ⚠️ **Common mistake:** The default XAMPP MySQL password is **empty** (`''`).  
> MySQL Workbench standalone installations typically have a password set during install.  
> Make sure this value matches exactly what you use to log into MySQL Workbench.

---

### Step 2 — PHP in PATH (Windows)

If `php` is not recognised in your terminal, PHP is not in your system PATH.

**Find where PHP is installed:**

| Setup | PHP Location |
|---|---|
| XAMPP (default) | `C:\xampp\php\php.exe` |
| WAMP | `C:\wamp64\bin\php\phpX.X.X\php.exe` |
| Laragon | `C:\laragon\bin\php\phpX.X.X\php.exe` |
| Standalone PHP | Wherever you installed it |

**Add to PATH permanently (Windows):**
1. Search → "Environment Variables" → Edit system environment variables
2. System variables → `Path` → Edit → New → paste the PHP folder path
3. Restart your terminal

**Or use the full path directly (quick fix):**
```powershell
C:\xampp\php\php.exe -S localhost:8080 -t "path\to\BizPulse"
```

---

### Step 3 — MySQL Client Compatibility (XAMPP + MySQL Workbench)

> ⚠️ **Important for users running XAMPP alongside MySQL Workbench (MySQL 8):**

XAMPP ships with its **own older MySQL client** (`C:\xampp\mysql\bin\mysql.exe`) that **cannot authenticate** against a standalone MySQL 8 server. This will cause errors like:

```
ERROR 1045: Plugin caching_sha2_password could not be loaded
```

**Solution — use the MySQL 8 client directly:**
```powershell
# Import schema using the correct MySQL 8 client
"C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe" -u root -pYOUR_PASSWORD < database\bizpulse.sql
```

**Or use the PHP setup script (easiest — no CLI issues):**
```powershell
C:\xampp\php\php.exe database\setup.php
```
This uses PHP's PDO (which works perfectly with MySQL 8) to create the database, table, and seed data automatically.

---

## 🗄 Database Setup

### Option A — PHP Setup Script (⭐ Recommended)

This is the easiest method — no MySQL CLI, no phpMyAdmin needed:

```powershell
# Make sure config/database.php credentials are set correctly first
C:\xampp\php\php.exe database\setup.php
```

Expected output:
```
=== BizPulse Database Setup ===
[1/4] Connected to MySQL 8.0.46
[2/4] Database 'bizpulse' ready.
[3/4] Table 'leads' ready.
[4/4] Seeded 5 sample leads.

✅ Setup complete! You can now run the app.
```

> Safe to run multiple times — uses `IF NOT EXISTS` and skips seeding if data already exists.

---

### Option B — phpMyAdmin

1. Open `http://localhost/phpmyadmin/`
2. Click **Import** → Choose file → select `database/bizpulse.sql`
3. Click **Go**

---

### Option C — MySQL Workbench

1. Open MySQL Workbench → connect to your server
2. File → Run SQL Script → select `database/bizpulse.sql`
3. Click **Run**

---

### Option D — MySQL 8 CLI

```bash
# Linux / macOS
mysql -u root -p < database/bizpulse.sql

# Windows PowerShell (use the MySQL 8 client, not XAMPP's)
"C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe" -u root -pYOUR_PASSWORD < database\bizpulse.sql
```

---

### Verify the setup

```sql
USE bizpulse;
SHOW TABLES;          -- should show: leads
DESCRIBE leads;       -- should show all 7 columns
SELECT COUNT(*) FROM leads;  -- should show 5 (if seeded)
```

---

## 🚀 How to Run

### Prerequisites

| Requirement | Version | Notes |
|---|---|---|
| PHP | 8.0+ | 8.2 recommended |
| MySQL | 8.0+ | Or MariaDB 10.6+ |
| Web Server | Any | XAMPP, WAMP, Laragon, or PHP built-in |

---

### Option A — PHP Built-in Server (Quickest)

```powershell
# Windows — full path if php is not in PATH
C:\xampp\php\php.exe -S localhost:8080 -t "d:\Code\BizPulse - Service & Lead Manager"

# If php IS in your PATH
php -S localhost:8080
```

Then open in your browser:

| URL | Page |
|---|---|
| `http://localhost:8080/` | Public landing page |
| `http://localhost:8080/admin.php` | Admin lead dashboard |

---

### Option B — XAMPP Apache (Traditional)

1. Start **Apache** from the XAMPP Control Panel.
2. Copy this project folder into:
   ```
   C:\xampp\htdocs\BizPulse\
   ```
3. Import the database (see Setup above).
4. Configure `config/database.php`.
5. Visit `http://localhost/BizPulse/` in your browser.

> ⚠️ If you do this, ensure XAMPP's Apache doesn't conflict with other local servers on port 80.

---

### Option C — VS Code + PHP Server Extension

1. Install the **PHP Server** extension in VS Code.
2. Right-click `index.php` → **PHP Server: Serve Project**.
3. It will auto-launch in your browser.

---

## 🧪 Running the Test Suite

A full automated verification script is included:

```powershell
# Make sure the PHP server is running first, then:
C:\xampp\php\php.exe database\verify.php
```

Expected: **43 PASSED / 0 FAILED** covering:
- Landing page HTML content
- Form submission (success + all rejection cases)
- Admin dashboard rendering
- AJAX endpoint (JSON, security guards, input validation)
- Database schema (all 7 columns, defaults)

---

## 🎓 Interview Talking Points

### Why PDO instead of mysqli?

PDO (PHP Data Objects) is preferred because:

- **Database-agnostic:** Works with MySQL, PostgreSQL, SQLite without changing code.
- **True prepared statements:** Prevents SQL injection at the driver level.
- **Exception-based errors:** Clean `try/catch` patterns, no silent failures.
- **Named placeholders:** `:name` instead of `?` keeps complex queries readable.

```php
// PDO — clean, safe, readable
$stmt = $pdo->prepare('SELECT * FROM leads WHERE id = :id');
$stmt->execute([':id' => $id]);
```

---

### Why Prepared Statements?

Prepared statements **separate SQL structure from data**. The database server compiles the query once, then safely receives values as parameters — user input is **never interpreted as SQL**.

```php
// ❌ VULNERABLE — never do this
$sql = "SELECT * FROM leads WHERE id = " . $_GET['id'];

// ✅ SAFE — data is bound separately
$stmt = $pdo->prepare('SELECT * FROM leads WHERE id = :id');
$stmt->execute([':id' => $_GET['id']]);
```

---

### Why AJAX / Fetch API?

Traditional status update flow: click → full page POST → server renders entire page → browser reloads.

With Fetch API: click → async POST → 50-byte JSON response → DOM updated inline. Result: instant, app-like UX.

```javascript
const response = await fetch('/update_status.php', {
    method: 'POST',
    headers: { 'X-Requested-With': 'XMLHttpRequest' },
    body: new URLSearchParams({ id: leadId }),
});
const data = await response.json();
if (data.success) { /* update badge colour inline — no reload */ }
```

---

### Why Tailwind CSS?

- **Utility-first:** No context-switching between HTML and separate CSS files.
- **Consistent design system:** Spacing, colour, and type scales enforced by config.
- **CDN for demos:** Zero build step needed for interview or portfolio demos.
- **Responsive by default:** `md:`, `lg:` breakpoint prefixes work out of the box.

---

### How is SQL Injection Prevented?

Three independent layers:

1. **Prepared statements** — data never interpolated into SQL strings.
2. **Input sanitisation** — `filter_var()`, `strip_tags()`, `trim()` applied to all inputs.
3. **Service whitelist** — the `service` field validated against a fixed allow-list.

```php
$allowedServices = ['Web Design', 'SEO Optimization', 'Content Management'];
if (!in_array($service, $allowedServices, true)) {
    redirectWithError('Invalid service selected.');
}
```

---

### How Does the Fetch API Update Status Without a Refresh?

```
[User clicks "Mark as Contacted"]
         │
         ▼
[admin.js — updateLeadStatus(id, btn)]
  → Button disabled, text = "Updating…"
         │
         ▼
[Fetch API — POST /update_status.php]
  Header: X-Requested-With: XMLHttpRequest
  Body:   id=42
         │
         ▼
[PHP validates:  ID is a positive integer ✓]
[PHP executes:   UPDATE leads SET status='Contacted' WHERE id=:id ✓]
[PHP returns:    {"success":true,"message":"Status updated"} ✓]
         │
         ▼
[JS receives JSON → DOM updated inline]
  → Badge: amber "New"  →  green "Contacted"
  → Button text: "✓ Contacted" (disabled)
  → Toast notification shown
  → Zero page reload ✓
```

---

### How is XSS Prevented?

All user-supplied data is **escaped at render time** using `htmlspecialchars()`:

```php
// Every output in admin.php goes through this
echo htmlspecialchars($lead['name'], ENT_QUOTES, 'UTF-8');
```

This converts `<script>alert('xss')</script>` → `&lt;script&gt;alert(&#039;xss&#039;)&lt;/script&gt;` before the browser sees it.

---

## 🔐 Security Checklist

- [x] PDO prepared statements (no string concatenation in SQL)
- [x] `filter_var()` for email validation
- [x] `strip_tags()` + `trim()` on all text inputs
- [x] Service field whitelisted against known-good values
- [x] `htmlspecialchars()` on all rendered output (XSS prevention)
- [x] POST-only enforcement on `submit.php` and `update_status.php` (405 on GET)
- [x] AJAX-only guard on `update_status.php` (`X-Requested-With` header check)
- [x] Integer validation on lead ID (`FILTER_VALIDATE_INT`, min=1)
- [x] Errors logged server-side via `error_log()` — friendly messages shown to users
- [x] PRG (Post/Redirect/Get) pattern prevents duplicate form submissions

---

## 🏗 Future Improvements

- [ ] Admin authentication (login/logout with PHP sessions)
- [ ] Pagination for large lead datasets
- [ ] Lead detail modal popup
- [ ] Email notification on new lead (PHPMailer / SMTP)
- [ ] CSV / Excel export
- [ ] Lead assignment to team members
- [ ] Full CRUD (edit + delete leads)
- [ ] Dashboard charts (Chart.js)

---

## 📄 License

This project is released for educational and portfolio purposes.  
Feel free to use, modify, and demo it in interviews.

---

_Built with ❤️ by **Sharavan Hiremath** using PHP 8.2, PDO, MySQL 8 & Tailwind CSS_
