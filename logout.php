<?php
/**
 * BizPulse — Logout (logout.php)
 *
 * Destroys the current session completely and redirects to the login page.
 *
 * Security:
 *  - Unsets all session variables
 *  - Destroys the session on the server
 *  - Expires the session cookie in the browser
 *  - No HTML output — immediate redirect
 */

require_once __DIR__ . '/includes/auth.php';

startSecureSession();

// ── 1. Clear all session data ─────────────────────────────────────────────
$_SESSION = [];

// ── 2. Expire the session cookie in the browser ───────────────────────────
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,   // Set expiry in the past to force deletion
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// ── 3. Destroy the server-side session ────────────────────────────────────
session_destroy();

// ── 4. Redirect to login page ─────────────────────────────────────────────
header('Location: /login.php?logged_out=1');
exit;
