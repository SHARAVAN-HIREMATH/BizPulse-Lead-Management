<?php
/**
 * BizPulse - Database Configuration
 *
 * Returns a PDO instance connected to the bizpulse MySQL database.
 * Edit the variables below to match your local environment.
 *
 * PDO is used over mysqli because it:
 *  - Supports multiple database drivers (MySQL, PostgreSQL, SQLite…)
 *  - Enforces prepared statements natively (prevents SQL injection)
 *  - Throws exceptions for clean, predictable error handling
 */

// ── Connection Parameters ────────────────────────────────────────────────────
// LOCAL DEVELOPMENT (XAMPP / MySQL Workbench)
$host     = '127.0.0.1';   // MySQL host — '127.0.0.1' forces TCP, avoiding socket auth issues
$database = 'bizpulse';    // Database name (must match the SQL script)
$username = 'root';        // MySQL username
$password = 'root';        // MySQL password
$charset  = 'utf8mb4';     // Character set — supports full Unicode + emoji

// ── PRODUCTION (InfinityFree) — uncomment and replace values below ────────
// $host     = 'sql302.infinityfree.com';
// $database = 'if0_42395565_bizpulse';
// $username = 'if0_42395565';
// $password = 'YOUR_PRODUCTION_PASSWORD';
// ────────────────────────────────────────────────────────────────────────────

/**
 * getDB()
 *
 * Returns a singleton PDO connection.
 * Singleton pattern avoids creating multiple connections per request.
 *
 * @return PDO
 * @throws PDOException on connection failure
 */
function getDB(): PDO
{
    // Import outer-scope variables into the function
    global $host, $database, $username, $password, $charset;

    // Singleton: store connection in a static variable
    static $pdo = null;

    if ($pdo === null) {
        // Data Source Name — tells PDO the driver, host, db, and charset
        $dsn = "mysql:host={$host};dbname={$database};charset={$charset}";

        // PDO options
        $options = [
            // Throw PDOException on every error (not silent failures)
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,

            // Return rows as associative arrays by default
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

            // Disable emulated prepares — forces true server-side prepared statements
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $pdo = new PDO($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            // In production, log this error instead of displaying it
            error_log('Database connection failed: ' . $e->getMessage());
            die(json_encode([
                'success' => false,
                'message' => 'Database connection failed. Please try again later.',
            ]));
        }
    }

    return $pdo;
}
