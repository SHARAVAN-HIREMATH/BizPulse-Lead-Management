<?php
/**
 * BizPulse — PHP Built-in Server Router (router.php)
 *
 * PURPOSE:
 *   The PHP built-in server (php -S) does NOT read .htaccess files.
 *   Without a router, every request for a non-existent file returns a blank
 *   PHP-generated 404 — the custom 404.php is never invoked.
 *
 *   This router runs BEFORE each request and handles two things:
 *    1. Serves existing static files (CSS, JS, images) directly
 *    2. Routes requests for non-existent paths to 404.php
 *
 * ROOT CAUSE FIX (Issue 2 — local development):
 *   Previously the server was started without a router:
 *     php -S localhost:8080 -t "path/to/project"
 *   Now start it with:
 *     php -S localhost:8080 router.php
 *   or use the helper script: start-server.bat
 *
 * HOW TO USE:
 *   Open a terminal in the project root and run:
 *     C:\xampp\php\php.exe -S localhost:8080 router.php
 *
 *   This file is ONLY for local development.
 *   On Apache / InfinityFree, .htaccess handles 404s instead.
 */

// ── Resolve the requested URI to a file path ──────────────────────────────
$requestUri  = $_SERVER['REQUEST_URI'];
$requestPath = parse_url($requestUri, PHP_URL_PATH); // Strip query string
$filePath    = __DIR__ . $requestPath;

// ── 1. Serve real files directly (PHP built-in server behaviour) ──────────
//    If the request maps to an actual file on disk, let PHP serve it.
//    This handles .php files, /assets/css/, /assets/js/, images, etc.
if (is_file($filePath)) {
    // Return false to tell the built-in server to handle this itself
    return false;
}

// ── 2. Serve real directories (e.g., direct folder access) ───────────────
//    If the path is a directory, let PHP try index.php inside it.
if (is_dir($filePath)) {
    return false;
}

// ── 3. Unknown path — serve custom 404 page ──────────────────────────────
//    The request path does not match any file or directory.
//    Include 404.php which sets http_response_code(404) and outputs the
//    branded error page. The browser URL stays unchanged (no redirect).
include __DIR__ . '/404.php';
// return true — tells the built-in server we handled the request
