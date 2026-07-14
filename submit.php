<?php
/**
 * BizPulse — Form Submission Handler (submit.php)
 *
 * Receives a POST request from the contact form on index.php.
 * Validates and sanitises all input, inserts into the database
 * using PDO prepared statements, then redirects (PRG pattern).
 *
 * Security:
 *  - filter_var() for email validation
 *  - htmlspecialchars() NOT applied here (applied on output)
 *  - Prepared statements prevent SQL injection entirely
 *  - POST-only access enforced
 *
 * Error handling (v2.1):
 *  - getDB() now throws PDOException instead of die(json_encode(...))
 *  - PDOException is caught and the user is redirected with an error message
 *  - Raw JSON is never shown in the browser
 */

// ── Only allow POST requests ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    header('Allow: POST');
    exit('405 Method Not Allowed. Please use the contact form.');
}

// ── Load database connection ──────────────────────────────────────────────
require_once __DIR__ . '/config/database.php';

// ── Helper: redirect with an error flag ──────────────────────────────────
function redirectWithError(string $message): never
{
    $encoded = urlencode($message);
    header("Location: /index.php?error={$encoded}");
    exit;
}

// ────────────────────────────────────────────────────────────────────────────
// 1. COLLECT RAW INPUT
//    Use null coalescing to avoid undefined index warnings.
// ────────────────────────────────────────────────────────────────────────────
$rawName    = $_POST['name']    ?? '';
$rawEmail   = $_POST['email']   ?? '';
$rawService = $_POST['service'] ?? '';
$rawMessage = $_POST['message'] ?? '';

// ────────────────────────────────────────────────────────────────────────────
// 2. SANITISE INPUT
//    - strip_tags:      remove any HTML/PHP tags
//    - trim:            remove leading/trailing whitespace
//    - filter_var:      validate and sanitise specific types
//
//    Note: We do NOT use htmlspecialchars() here.
//          We store raw (but stripped) data in the DB and escape on OUTPUT.
// ────────────────────────────────────────────────────────────────────────────
$name    = trim(strip_tags($rawName));
$email   = trim(filter_var($rawEmail, FILTER_SANITIZE_EMAIL));
$service = trim(strip_tags($rawService));
$message = trim(strip_tags($rawMessage));

// ────────────────────────────────────────────────────────────────────────────
// 3. SERVER-SIDE VALIDATION
//    This runs even if JavaScript validation is bypassed.
// ────────────────────────────────────────────────────────────────────────────

// 3a. Required fields — must not be empty
if (empty($name) || empty($email) || empty($service) || empty($message)) {
    redirectWithError('All fields are required. Please fill in the form completely.');
}

// 3b. Length limits — prevent oversized payloads
if (strlen($name) > 100) {
    redirectWithError('Name must not exceed 100 characters.');
}
if (strlen($email) > 150) {
    redirectWithError('Email address must not exceed 150 characters.');
}
if (strlen($message) > 2000) {
    redirectWithError('Message must not exceed 2000 characters.');
}

// 3c. Email format validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    redirectWithError('Please provide a valid email address.');
}

// 3d. Whitelist allowed services (prevents arbitrary values being stored)
$allowedServices = ['Web Design', 'SEO Optimization', 'Content Management'];
if (!in_array($service, $allowedServices, true)) {
    redirectWithError('Please select a valid service from the list.');
}

// 3e. Minimum message length
if (strlen($message) < 10) {
    redirectWithError('Message must be at least 10 characters long.');
}

// ────────────────────────────────────────────────────────────────────────────
// 4. DATABASE INSERTION
//    Uses a prepared statement — the values are NEVER concatenated into SQL.
//    PDO binds the parameters separately, so SQL injection is impossible.
// ────────────────────────────────────────────────────────────────────────────
try {
    $pdo = getDB();

    // Prepare the statement with named placeholders
    $stmt = $pdo->prepare(
        'INSERT INTO leads (name, email, service, message)
         VALUES (:name, :email, :service, :message)'
    );

    // Bind values — PDO handles quoting and escaping automatically
    $stmt->execute([
        ':name'    => $name,
        ':email'   => $email,
        ':service' => $service,
        ':message' => $message,
    ]);

    // ── Success ───────────────────────────────────────────────────────────
    // Redirect using POST/Redirect/GET pattern to prevent duplicate submissions
    header('Location: /index.php?success=1');
    exit;

} catch (PDOException $e) {
    // Log the full technical error internally; redirect user with a friendly message.
    // Never expose exception details to the browser.
    error_log('[BizPulse] Lead insertion failed: ' . $e->getMessage());
    redirectWithError('Unable to process your enquiry. Please try again later.');
}
