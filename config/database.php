<?php
/**
 * BizPulse — Database Configuration (config/database.php)
 *
 * Returns a singleton PDO connection to the bizpulse MySQL database.
 * Edit the variables in the "Connection Parameters" section below.
 *
 * Why PDO over mysqli:
 *  - Driver-agnostic (MySQL, PostgreSQL, SQLite…)
 *  - Native prepared statements (prevents SQL injection)
 *  - Exception-based error handling for clean, predictable control flow
 *
 * ──────────────────────────────────────────────────────────────────────────
 * ROOT CAUSE FIX (v2.1):
 *
 * The previous version used die(json_encode(...)) inside getDB() on failure.
 * That caused raw JSON to appear in the browser on ALL pages (including UI
 * pages like login.php and admin.php) whenever the database was unreachable.
 *
 * The correct pattern is:
 *  - getDB() THROWS a PDOException on failure (never outputs anything)
 *  - UI pages (login, admin, submit) CATCH it and render a clean HTML error
 *  - AJAX endpoints (update_status) CATCH it and return a JSON error response
 *
 * This way, each caller decides how the error is communicated to the user.
 * ──────────────────────────────────────────────────────────────────────────
 */

// ── Connection Parameters ──────────────────────────────────────────────────
//
// LOCAL DEVELOPMENT (XAMPP + MySQL Workbench):
//   Use '127.0.0.1' instead of 'localhost'.
//   On Windows with MySQL 8, 'localhost' triggers named-pipe authentication
//   which can fail in the PHP built-in server even when the password is correct.
//   '127.0.0.1' forces TCP/IP and is reliable in all contexts (CLI, built-in
//   server, and Apache).
//
$host     = '127.0.0.1';  // TCP/IP — works in CLI, built-in server, and Apache
$database = 'bizpulse';   // Database name (must match the SQL setup script)
$username = 'root';        // MySQL username
$password = 'root';        // MySQL password
$charset  = 'utf8mb4';     // Full Unicode support (emoji, multilingual text)

// ── PRODUCTION (InfinityFree) ──────────────────────────────────────────────
// To deploy to InfinityFree, comment out the local block above and uncomment:
//
// $host     = 'sql302.infinityfree.com';
// $database = 'if0_42395565_bizpulse';
// $username = 'if0_42395565';
// $password = 'Shravan3512';
//
// InfinityFree Notes:
//  - The host is provided in your InfinityFree control panel
//  - Do NOT use localhost on InfinityFree — use the exact SQL host provided
//  - The database name must be prefixed with your account ID (e.g., if0_42395565_)
// ──────────────────────────────────────────────────────────────────────────

/**
 * getDB()
 *
 * Returns a singleton PDO connection.
 *
 * The singleton pattern avoids opening a new database connection on every
 * call within the same request lifecycle, saving overhead.
 *
 * IMPORTANT: This function THROWS a PDOException if the connection fails.
 * It never calls die() or echo. The CALLING PAGE is responsible for catching
 * the exception and displaying an appropriate error to the user.
 *
 * @return PDO  A connected, configured PDO instance
 * @throws PDOException  If the connection cannot be established
 */
function getDB(): PDO
{
    global $host, $database, $username, $password, $charset;

    // Singleton: reuse the same connection within a single request
    static $pdo = null;

    if ($pdo === null) {
        $dsn = "mysql:host={$host};dbname={$database};charset={$charset}";

        $options = [
            // Throw a PDOException on every error (not silent failures)
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,

            // Return rows as associative arrays by default
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

            // Use true prepared statements (not emulated), preventing
            // any possibility of SQL injection at the driver level
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        // Let the PDOException propagate to the caller.
        // The caller (login.php, admin.php, update_status.php, submit.php)
        // will catch it and handle it appropriately for their context
        // (HTML error for UI pages, JSON error for AJAX endpoints).
        $pdo = new PDO($dsn, $username, $password, $options);
    }

    return $pdo;
}
