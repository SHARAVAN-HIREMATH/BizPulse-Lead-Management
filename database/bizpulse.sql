-- ============================================================
-- BizPulse — Database Schema v2
-- ============================================================
--
-- Changes in v2:
--   + Added `users` table for admin authentication
--   + Improved table comments and character set declarations
--
-- To import:
--   OPTION A (Recommended): php database/setup.php
--   OPTION B: Run this file in MySQL Workbench or phpMyAdmin
--   OPTION C: mysql -u root -pYOUR_PASS bizpulse < database/bizpulse.sql
--
-- Default admin credentials (seeded by setup.php):
--   Email   : admin@bizpulse.com
--   Password: Admin@123
-- ============================================================

-- ── Create and select the database ────────────────────────────────────────
CREATE DATABASE IF NOT EXISTS bizpulse
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE bizpulse;

-- ════════════════════════════════════════════════════════════
-- TABLE: leads
-- Stores all customer enquiries submitted via the contact form.
-- ════════════════════════════════════════════════════════════
CREATE TABLE IF NOT EXISTS leads (
    id         INT          NOT NULL AUTO_INCREMENT COMMENT 'Primary key',
    name       VARCHAR(100) NOT NULL               COMMENT 'Customer full name',
    email      VARCHAR(150) NOT NULL               COMMENT 'Customer email address',
    service    VARCHAR(100) NOT NULL               COMMENT 'Service requested (whitelisted value)',
    message    TEXT         NOT NULL               COMMENT 'Customer enquiry message',
    status     VARCHAR(20)  NOT NULL DEFAULT 'New' COMMENT 'Lead status: New | Contacted',
    created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Submission timestamp',

    PRIMARY KEY (id)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Customer enquiries submitted via the contact form';

-- ════════════════════════════════════════════════════════════
-- TABLE: users
-- Stores admin accounts for dashboard authentication.
-- Passwords stored as bcrypt hashes (password_hash PHP).
-- ════════════════════════════════════════════════════════════
CREATE TABLE IF NOT EXISTS users (
    id         INT          NOT NULL AUTO_INCREMENT COMMENT 'Primary key',
    name       VARCHAR(100) NOT NULL               COMMENT 'Admin display name',
    email      VARCHAR(150) NOT NULL               COMMENT 'Login email (unique)',
    password   VARCHAR(255) NOT NULL               COMMENT 'bcrypt hash via password_hash()',
    role       VARCHAR(20)  NOT NULL DEFAULT 'admin' COMMENT 'User role: admin',
    created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Account creation timestamp',

    PRIMARY KEY (id),
    UNIQUE KEY uq_email (email)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Admin users for the BizPulse dashboard';

-- ════════════════════════════════════════════════════════════
-- SEED: Sample leads (run only if table is empty)
-- ════════════════════════════════════════════════════════════
INSERT INTO leads (name, email, service, message, status, created_at)
SELECT * FROM (VALUES
    ('Alice Johnson',   'alice@example.com',   'Web Design',        'I need a new website for my bakery business. It should be mobile-friendly and include an online ordering system.',    'New',       NOW() - INTERVAL 6 DAY),
    ('Bob Martinez',    'bob@example.com',     'SEO Optimization',  'Our current website gets very little traffic. I want to improve our Google rankings for local search terms.',        'Contacted', NOW() - INTERVAL 5 DAY),
    ('Carol Williams',  'carol@example.com',   'Content Management', 'I run a lifestyle blog and need help with a regular posting schedule and SEO-friendly content.',                     'New',       NOW() - INTERVAL 4 DAY),
    ('David Chen',      'david@example.com',   'Web Design',        'Looking for a professional portfolio website to showcase my graphic design work.',                                   'New',       NOW() - INTERVAL 3 DAY),
    ('Emily Rose',      'emily@example.com',   'SEO Optimization',  'We recently launched an e-commerce store but our organic sales are low. Need full SEO audit and strategy.',         'Contacted', NOW() - INTERVAL 2 DAY)
) AS tmp (name, email, service, message, status, created_at)
WHERE NOT EXISTS (SELECT 1 FROM leads LIMIT 1);

-- ════════════════════════════════════════════════════════════
-- SEED: Admin user
-- NOTE: The password hash below is for 'Admin@123'.
--       For security, run `php database/setup.php` instead —
--       it generates a fresh bcrypt hash at install time.
--
-- password_hash('Admin@123', PASSWORD_BCRYPT)
-- ════════════════════════════════════════════════════════════
-- Admin is seeded by setup.php to ensure a fresh bcrypt salt.
-- If you must insert manually, generate the hash first:
--   php -r "echo password_hash('Admin@123', PASSWORD_BCRYPT);"
-- Then replace the placeholder below:
--
-- INSERT IGNORE INTO users (name, email, password, role) VALUES
-- ('Admin', 'admin@bizpulse.com', '<paste-bcrypt-hash-here>', 'admin');
