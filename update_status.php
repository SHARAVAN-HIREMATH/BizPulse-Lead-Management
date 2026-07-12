<?php
/**
 * BizPulse — AJAX Status Update Handler (update_status.php)
 *
 * Receives a POST request from admin.js via the Fetch API.
 * Validates the lead ID and updates its status to "Contacted".
 *
 * Always returns JSON — never HTML.
 *
 * Security:
 *  - Only POST requests accepted
 *  - Only AJAX requests accepted (X-Requested-With header check)
 *  - ID validated as a positive integer
 *  - PDO prepared statement used for the UPDATE
 *  - No output buffering issues (header sent before any output)
 */

// ── Force JSON response headers immediately ───────────────────────────────
header('Content-Type: application/json; charset=utf-8');

// ── Helper: send JSON and terminate ──────────────────────────────────────
function jsonResponse(bool $success, string $message = '', int $httpCode = 200): never
{
    http_response_code($httpCode);
    echo json_encode([
        'success' => $success,
        'message' => $message,
    ], JSON_THROW_ON_ERROR);
    exit;
}

// ────────────────────────────────────────────────────────────────────────────
// 1. REQUEST METHOD CHECK — only POST allowed
// ────────────────────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Method not allowed.', 405);
}

// ────────────────────────────────────────────────────────────────────────────
// 2. AJAX CHECK — verify the Fetch API set the custom header
//    Prevents casual browser navigation to this endpoint
// ────────────────────────────────────────────────────────────────────────────
$isAjax = ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest';
if (!$isAjax) {
    jsonResponse(false, 'AJAX requests only.', 403);
}

// ────────────────────────────────────────────────────────────────────────────
// 3. INPUT VALIDATION
//    FILTER_VALIDATE_INT ensures the ID is a real integer > 0.
//    Rejects strings, negative numbers, floats, and injection attempts.
// ────────────────────────────────────────────────────────────────────────────
$rawId = $_POST['id'] ?? null;

$id = filter_var($rawId, FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1],
]);

if ($id === false || $id === null) {
    jsonResponse(false, 'Invalid lead ID provided.', 422);
}

// ────────────────────────────────────────────────────────────────────────────
// 4. DATABASE UPDATE
//    Only updates rows that are currently 'New' — idempotent by design.
// ────────────────────────────────────────────────────────────────────────────
require_once __DIR__ . '/config/database.php';

try {
    $pdo = getDB();

    // Prepared statement — :id is bound separately, never concatenated
    $stmt = $pdo->prepare(
        "UPDATE leads
         SET    status = 'Contacted'
         WHERE  id     = :id
         AND    status = 'New'"
    );

    $stmt->execute([':id' => $id]);

    // rowCount() tells us if a row was actually updated
    if ($stmt->rowCount() > 0) {
        jsonResponse(true, 'Status updated to Contacted.');
    } else {
        // Either the lead doesn't exist or was already Contacted
        // Return success:true — front-end can handle this gracefully
        jsonResponse(true, 'No update needed (already Contacted or ID not found).');
    }

} catch (PDOException $e) {
    error_log('Status update failed: ' . $e->getMessage());
    jsonResponse(false, 'Database error. Please try again.', 500);
}
