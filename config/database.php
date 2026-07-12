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
$host     = 'localhost';   // MySQL host (use 127.0.0.1 if localhost fails)
$database = 'bizpulse';   // Database name
$username = 'root';       // MySQL username
$password = 'root';       // MySQL password
$charset  = 'utf8mb4';    // Character set — supports full Unicode + emoji
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
