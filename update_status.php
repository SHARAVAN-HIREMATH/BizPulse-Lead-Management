<?php
/**
 * BizPulse — AJAX Status Update Handler (update_status.php) v2
 *
 * Changes in v2:
 *  - requireAuth() guard — only logged-in admins can update statuses
 *  - Returns updated dashboard statistics in the JSON response
 *    so admin.js can update the stat counters without a page reload
 *
 * Always returns JSON — never HTML.
 *
 * Error handling (v2.1):
 *  - getDB() now throws PDOException instead of die(json_encode(...))
 *  - This endpoint's own catch(PDOException) at the bottom handles DB errors
 *    and returns a proper JSON error response — this is correct for AJAX endpoints
 *
 * Security:
 *  - Authenticated session required
 *  - POST + X-Requested-With: XMLHttpRequest required
 *  - Lead ID validated as positive integer (FILTER_VALIDATE_INT)
 *  - PDO prepared statement for the UPDATE
 */

// ── Force JSON headers immediately (no output buffering issues) ───────────
header('Content-Type: application/json; charset=utf-8');

// ── Auth guard ────────────────────────────────────────────────────────────
require_once __DIR__ . '/includes/auth.php';

// If not authenticated, return 401 JSON (not a redirect)
startSecureSession();
if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorised.'], JSON_THROW_ON_ERROR);
    exit;
}

// ── Helper: emit JSON and exit ────────────────────────────────────────────
function jsonResponse(bool $success, string $message = '', int $httpCode = 200, array $extra = []): never
{
    http_response_code($httpCode);
    $payload = array_merge(['success' => $success, 'message' => $message], $extra);
    echo json_encode($payload, JSON_THROW_ON_ERROR);
    exit;
}

// ────────────────────────────────────────────────────────────────────────────
// 1. METHOD CHECK
// ────────────────────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Method not allowed.', 405);
}

// ────────────────────────────────────────────────────────────────────────────
// 2. AJAX CHECK
// ────────────────────────────────────────────────────────────────────────────
if (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') !== 'XMLHttpRequest') {
    jsonResponse(false, 'AJAX requests only.', 403);
}

// ────────────────────────────────────────────────────────────────────────────
// 3. INPUT VALIDATION
// ────────────────────────────────────────────────────────────────────────────
$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1],
]);

if ($id === false || $id === null) {
    jsonResponse(false, 'Invalid lead ID provided.', 422);
}

// ────────────────────────────────────────────────────────────────────────────
// 4. DATABASE UPDATE + STATS QUERY
// ────────────────────────────────────────────────────────────────────────────
require_once __DIR__ . '/config/database.php';

try {
    $pdo = getDB();

    // Update the lead status (prepared statement — no SQL injection possible)
    $stmt = $pdo->prepare(
        "UPDATE leads
         SET    status = 'Contacted'
         WHERE  id     = :id
         AND    status = 'New'"
    );
    $stmt->execute([':id' => $id]);

    $updated = $stmt->rowCount() > 0;

    // ── Fetch fresh statistics for the dashboard counters ────────────────
    // Returned to admin.js to update #stat-total, #stat-new, #stat-contacted
    // without requiring a page reload.
    $total     = (int) $pdo->query('SELECT COUNT(*) FROM leads')->fetchColumn();
    $newCount  = (int) $pdo->query("SELECT COUNT(*) FROM leads WHERE status = 'New'")->fetchColumn();
    $contacted = (int) $pdo->query("SELECT COUNT(*) FROM leads WHERE status = 'Contacted'")->fetchColumn();

    $stats = [
        'total'     => $total,
        'newCount'  => $newCount,
        'contacted' => $contacted,
    ];

    $message = $updated
        ? 'Status updated to Contacted.'
        : 'No update needed (already Contacted or ID not found).';

    jsonResponse(true, $message, 200, ['stats' => $stats]);

} catch (PDOException $e) {
    error_log('Status update failed: ' . $e->getMessage());
    jsonResponse(false, 'Database error. Please try again.', 500);
}
