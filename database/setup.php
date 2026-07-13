<?php
/**
 * BizPulse — Database Setup Script v2 (database/setup.php)
 *
 * Run this once (or anytime) to initialise or upgrade the database.
 * Safe to run on an existing installation — all operations use
 * IF NOT EXISTS / INSERT IGNORE to prevent data loss.
 *
 * Usage:
 *   php database/setup.php
 *
 * What it does:
 *   1. Connects to MySQL using credentials from config/database.php
 *   2. Creates the `bizpulse` database if it doesn't exist
 *   3. Creates the `leads` table if it doesn't exist
 *   4. Creates the `users` table if it doesn't exist   [NEW in v2]
 *   5. Seeds 5 sample leads (only if the table is empty)
 *   6. Seeds the default admin user (only if not present) [NEW in v2]
 */

// Correct path even when run from different directories
define('BASE_DIR', dirname(__DIR__));

require_once BASE_DIR . '/config/database.php';

// ── Terminal output helpers ───────────────────────────────────────────────
$isCli = (PHP_SAPI === 'cli');

function out(string $msg, bool $isErr = false): void
{
    global $isCli;
    if ($isCli) {
        echo $msg . PHP_EOL;
    } else {
        // HTML output when accessed through browser
        $style = $isErr ? 'color:red' : 'color:inherit';
        echo '<pre style="' . $style . '">' . htmlspecialchars($msg) . '</pre>';
    }
}

function success(string $msg): void { out('[OK]  ' . $msg); }
function fail(string $msg): void    { out('[FAIL] ' . $msg, true); exit(1); }
function info(string $msg): void    { out('      ' . $msg); }

// ── Banner ─────────────────────────────────────────────────────────────────
if (!$isCli) {
    echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>BizPulse Setup</title>';
    echo '<style>body{font-family:monospace;background:#0f172a;color:#94a3b8;padding:2rem;}</style>';
    echo '</head><body>';
}

out('');
out('=== BizPulse Database Setup v2 ===');
out('');

// ── Connect to MySQL (without selecting a database yet) ───────────────────
try {
    $dsn = 'mysql:host=localhost;charset=utf8mb4';
    $pdo = new PDO($dsn, 'root', 'root', [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    success('[1/6] Connected to MySQL ' . $pdo->getAttribute(PDO::ATTR_SERVER_VERSION));
} catch (PDOException $e) {
    fail('[1/6] Cannot connect to MySQL: ' . $e->getMessage());
}

// ── 2. Create database ────────────────────────────────────────────────────
try {
    $pdo->exec("CREATE DATABASE IF NOT EXISTS bizpulse CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE bizpulse");
    success('[2/6] Database `bizpulse` is ready.');
} catch (PDOException $e) {
    fail('[2/6] Failed to create database: ' . $e->getMessage());
}

// ── 3. Create `leads` table ───────────────────────────────────────────────
try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS leads (
            id         INT          NOT NULL AUTO_INCREMENT,
            name       VARCHAR(100) NOT NULL,
            email      VARCHAR(150) NOT NULL,
            service    VARCHAR(100) NOT NULL,
            message    TEXT         NOT NULL,
            status     VARCHAR(20)  NOT NULL DEFAULT 'New',
            created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    success('[3/6] Table `leads` is ready.');
} catch (PDOException $e) {
    fail('[3/6] Failed to create `leads` table: ' . $e->getMessage());
}

// ── 4. Create `users` table [v2] ─────────────────────────────────────────
try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id         INT          NOT NULL AUTO_INCREMENT,
            name       VARCHAR(100) NOT NULL,
            email      VARCHAR(150) NOT NULL,
            password   VARCHAR(255) NOT NULL,
            role       VARCHAR(20)  NOT NULL DEFAULT 'admin',
            created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY uq_email (email)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    success('[4/6] Table `users` is ready.');
} catch (PDOException $e) {
    fail('[4/6] Failed to create `users` table: ' . $e->getMessage());
}

// ── 5. Seed sample leads (only if empty) ─────────────────────────────────
try {
    $count = (int) $pdo->query('SELECT COUNT(*) FROM leads')->fetchColumn();

    if ($count === 0) {
        $sampleLeads = [
            ['Alice Johnson',   'alice@example.com',   'Web Design',         'I need a new website for my bakery business. Mobile-friendly with an online menu.',        'New',       '-6 days'],
            ['Bob Martinez',    'bob@example.com',     'SEO Optimization',   'Our website gets very little organic traffic. Need Google rankings improvement.',          'Contacted', '-5 days'],
            ['Carol Williams',  'carol@example.com',   'Content Management', 'I run a lifestyle blog and need help with SEO-friendly content and posting schedule.',     'New',       '-4 days'],
            ['David Chen',      'david@example.com',   'Web Design',         'Looking for a professional portfolio website to showcase my graphic design work.',         'New',       '-3 days'],
            ['Emily Rose',      'emily@example.com',   'SEO Optimization',   'Recently launched e-commerce but organic sales are low. Need full SEO audit.',            'Contacted', '-2 days'],
            ['Frank O\'Brien',  'frank@example.com',   'Web Design',         'Need a landing page for my coaching business with email opt-in integration.',             'New',       '-1 day'],
            ['Grace Kim',       'grace@example.com',   'Content Management', 'Looking for monthly blog posts and social media captions for my wellness brand.',         'New',        '0'],
            ['Henry Walsh',     'henry@example.com',   'SEO Optimization',   'My local plumbing business needs to appear in Google Maps and local search results.',     'Contacted', '-7 days'],
        ];

        $stmt = $pdo->prepare(
            "INSERT INTO leads (name, email, service, message, status, created_at)
             VALUES (:name, :email, :service, :message, :status, :created_at)"
        );

        foreach ($sampleLeads as [$name, $email, $service, $message, $status, $offset]) {
            $ts = $offset === '0' ? date('Y-m-d H:i:s') : date('Y-m-d H:i:s', strtotime($offset));
            $stmt->execute([
                ':name'       => $name,
                ':email'      => $email,
                ':service'    => $service,
                ':message'    => $message,
                ':status'     => $status,
                ':created_at' => $ts,
            ]);
        }
        success('[5/6] Seeded ' . count($sampleLeads) . ' sample leads.');
    } else {
        success('[5/6] Leads table already has ' . $count . ' record(s) — skipping seed.');
    }
} catch (PDOException $e) {
    fail('[5/6] Lead seeding failed: ' . $e->getMessage());
}

// ── 6. Seed admin user [v2] ───────────────────────────────────────────────
try {
    $adminEmail = 'admin@bizpulse.com';
    $exists = (int) $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = :email')
                        ->execute([':email' => $adminEmail]) &&
              (int) $pdo->query("SELECT COUNT(*) FROM users WHERE email = '{$adminEmail}'")->fetchColumn();

    // Re-query cleanly
    $stmt   = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = :email');
    $stmt->execute([':email' => $adminEmail]);
    $exists = (int) $stmt->fetchColumn();

    if ($exists === 0) {
        // Generate a fresh bcrypt hash (cost factor 12 — strong but fast enough)
        $plainPassword = 'Admin@123';
        $hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT, ['cost' => 12]);

        $stmt = $pdo->prepare(
            "INSERT INTO users (name, email, password, role)
             VALUES (:name, :email, :password, :role)"
        );
        $stmt->execute([
            ':name'     => 'Admin',
            ':email'    => $adminEmail,
            ':password' => $hashedPassword,
            ':role'     => 'admin',
        ]);

        success('[6/6] Admin user created.');
        info('  Email   : ' . $adminEmail);
        info('  Password: ' . $plainPassword . '  ← Change this after first login!');
    } else {
        success('[6/6] Admin user already exists — skipping.');
    }

} catch (PDOException $e) {
    fail('[6/6] Admin seeding failed: ' . $e->getMessage());
}

// ── Summary ────────────────────────────────────────────────────────────────
out('');
out('==================================================');
out(' ✅  Setup complete! You can now run the app.');
out('');
out(' 🌐  http://localhost:8080/           (landing page)');
out(' 🔐  http://localhost:8080/login.php  (admin login)');
out(' 📊  http://localhost:8080/admin.php  (dashboard)');
out('');
out(' Admin login:  admin@bizpulse.com / Admin@123');
out('==================================================');
out('');

if (!$isCli) {
    echo '</body></html>';
}
