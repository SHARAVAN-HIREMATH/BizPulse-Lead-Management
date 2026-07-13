<?php
/**
 * BizPulse — Authentication & Authorization Helpers (includes/auth.php)
 *
 * Centralises all session and auth logic.
 * Include this file on any page that needs session awareness.
 *
 * Public API:
 *   startSecureSession() — start a hardened PHP session (idempotent)
 *   requireAuth()        — redirect to login if not authenticated
 *   isLoggedIn()         — boolean check without redirect
 *   currentUser()        — return the authenticated user's data
 */

/**
 * Start a secure PHP session exactly once.
 *
 * Security settings applied:
 *  - HTTPOnly   : session cookie is inaccessible to JavaScript
 *  - SameSite   : Lax prevents CSRF from cross-site links
 *  - No expiry  : cookie lives until browser is closed
 */
function startSecureSession(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params([
            'lifetime' => 0,        // Session cookie (expires on browser close)
            'path'     => '/',      // Accessible across the whole site
            'httponly' => true,     // JavaScript cannot access the cookie
            'samesite' => 'Lax',   // Mitigates CSRF via cross-site navigation
        ]);
        session_start();
    }
}

/**
 * Require the visitor to be an authenticated admin.
 *
 * Must be called at the VERY TOP of any protected page,
 * BEFORE any HTML output is sent.
 * Redirects to /login.php if the check fails.
 */
function requireAuth(): void
{
    startSecureSession();

    if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
        header('Location: /login.php');
        exit;
    }
}

/**
 * Check if the current visitor is a logged-in admin.
 * Non-redirecting — safe to use for conditional UI rendering.
 *
 * @return bool
 */
function isLoggedIn(): bool
{
    startSecureSession();
    return !empty($_SESSION['user_id']) && ($_SESSION['role'] ?? '') === 'admin';
}

/**
 * Return the current session user's data.
 * Returns an empty-safe array even when not logged in.
 *
 * @return array{id: int|null, name: string, email: string, role: string}
 */
function currentUser(): array
{
    startSecureSession();
    return [
        'id'    => $_SESSION['user_id']    ?? null,
        'name'  => $_SESSION['user_name']  ?? 'Admin',
        'email' => $_SESSION['user_email'] ?? '',
        'role'  => $_SESSION['role']       ?? '',
    ];
}
