-- ============================================================
-- BizPulse - Service & Lead Manager
-- Database Schema
-- ============================================================

-- Create and select the database
CREATE DATABASE IF NOT EXISTS bizpulse
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE bizpulse;

-- ============================================================
-- Table: leads
-- Stores all customer enquiries submitted via the landing page
-- ============================================================
CREATE TABLE IF NOT EXISTS leads (
    id          INT             NOT NULL AUTO_INCREMENT,
    name        VARCHAR(100)    NOT NULL,
    email       VARCHAR(150)    NOT NULL,
    service     VARCHAR(100)    NOT NULL,
    message     TEXT            NOT NULL,
    status      VARCHAR(50)     NOT NULL DEFAULT 'New',
    created_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Sample seed data (optional – comment out in production)
-- ============================================================
INSERT INTO leads (name, email, service, message, status, created_at) VALUES
('Alice Johnson',  'alice@example.com',  'Web Design',         'Looking for a modern portfolio website for my photography studio.',       'Contacted', NOW() - INTERVAL 5 DAY),
('Bob Martinez',   'bob@example.com',    'SEO Optimization',   'We need to improve our Google ranking for local plumbing services.',      'New',        NOW() - INTERVAL 3 DAY),
('Carol White',    'carol@example.com',  'Content Management', 'Our blog needs regular updates. Looking for a content management team.',  'New',        NOW() - INTERVAL 2 DAY),
('David Brown',    'david@example.com',  'Web Design',         'E-commerce store redesign. We sell handmade jewellery.',                  'Contacted', NOW() - INTERVAL 1 DAY),
('Emma Davis',     'emma@example.com',   'SEO Optimization',   'Startup looking for full digital marketing strategy and SEO setup.',     'New',        NOW());
