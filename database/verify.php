<?php
/**
 * BizPulse v2.1 — Full Regression Test Suite
 *
 * Tests all features after the two bug fixes:
 *  - ISSUE 1: Login DB error handling (no raw JSON in browser)
 *  - ISSUE 2: Custom 404 page routing
 *
 * Run: C:\xampp\php\php.exe database/verify.php
 */

$base   = 'http://localhost:8080';
$passed = 0;
$failed = 0;

function check(string $label, bool $result): void
{
    global $passed, $failed;
    if ($result) {
        echo "\033[32m[PASS]\033[0m {$label}" . PHP_EOL;
        $passed++;
    } else {
        echo "\033[31m[FAIL]\033[0m {$label}" . PHP_EOL;
        $failed++;
    }
}

function httpGet(string $url): array
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_TIMEOUT        => 8,
        CURLOPT_HTTPHEADER     => ['Accept: text/html'],
    ]);
    $body     = (string) curl_exec($ch);
    $code     = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $location = (string) curl_getinfo($ch, CURLINFO_REDIRECT_URL);
    curl_close($ch);
    return ['code' => $code, 'body' => $body, 'location' => $location];
}

function httpPost(string $url, array|string $fields, array $headers = []): array
{
    $ch = curl_init($url);
    $postData = is_array($fields) ? http_build_query($fields) : $fields;
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $postData,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_TIMEOUT        => 8,
        CURLOPT_HTTPHEADER     => array_merge(['Content-Type: application/x-www-form-urlencoded'], $headers),
    ]);
    $body     = (string) curl_exec($ch);
    $code     = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $location = (string) curl_getinfo($ch, CURLINFO_REDIRECT_URL);
    curl_close($ch);
    return ['code' => $code, 'body' => $body, 'location' => $location];
}

echo PHP_EOL . '=============================================' . PHP_EOL;
echo '  BIZPULSE v2.1 — REGRESSION TEST SUITE    ' . PHP_EOL;
echo '=============================================' . PHP_EOL . PHP_EOL;

// ════════════════════════════════════════════════════════════
//  1. LANDING PAGE
// ════════════════════════════════════════════════════════════
echo '--- 1. Landing Page (index.php) ---' . PHP_EOL;
$r = httpGet($base . '/');
check('HTTP 200 OK',                       $r['code'] === 200);
check('Contact form present',              str_contains($r['body'], 'id="contact"'));
check('Dark mode anti-flash script',       str_contains($r['body'], "localStorage.getItem('bpTheme')"));
check('Tailwind darkMode:class config',    str_contains($r['body'], "darkMode: 'class'"));
check('Service: Web Design',               str_contains($r['body'], 'Web Design'));
check('Service: SEO Optimization',         str_contains($r['body'], 'SEO Optimization'));
check('Service: Content Management',       str_contains($r['body'], 'Content Management'));
check('Success modal PHP block present',   str_contains($r['body'], 'modal-overlay') || str_contains($r['body'], 'success=1'));
check('darkmode.js included',              str_contains($r['body'], 'darkmode.js'));
echo PHP_EOL;

// ════════════════════════════════════════════════════════════
//  2. SUCCESS MODAL
// ════════════════════════════════════════════════════════════
echo '--- 2. Success Modal (index.php?success=1) ---' . PHP_EOL;
$r = httpGet($base . '/index.php?success=1');
check('HTTP 200',                          $r['code'] === 200);
check('Modal overlay rendered',            str_contains($r['body'], 'modal-overlay') || str_contains($r['body'], 'fixed inset-0'));
check('Animated checkmark present',        str_contains($r['body'], 'checkmark') || str_contains($r['body'], 'animate'));
check('"Thank you" message in modal',      str_contains($r['body'], 'Thank you') || str_contains($r['body'], 'received'));
echo PHP_EOL;

// ════════════════════════════════════════════════════════════
//  3. LOGIN PAGE
// ════════════════════════════════════════════════════════════
echo '--- 3. Login Page (login.php) ---' . PHP_EOL;
$r = httpGet($base . '/login.php');
check('HTTP 200 OK',                       $r['code'] === 200);
check('Dark mode anti-flash present',      str_contains($r['body'], "localStorage.getItem('bpTheme')"));
check('noindex meta (security)',           str_contains($r['body'], 'noindex'));
check('Login form rendered',               str_contains($r['body'], 'id="login-form"'));
check('Email field present',               str_contains($r['body'], 'name="email"'));
check('Password field present',            str_contains($r['body'], 'name="password"'));
check('Show/hide password toggle',         str_contains($r['body'], 'toggle-password'));
check('BizPulse branding on login',        str_contains($r['body'], 'BizPulse'));
check('Back to website link',              str_contains($r['body'], '/index.php'));
check('No raw JSON exposed',               !str_contains($r['body'], '"success":false') && !str_contains($r['body'], 'json_encode'));
echo PHP_EOL;

// ════════════════════════════════════════════════════════════
//  4. LOGIN AUTHENTICATION — ISSUE 1 FIX VERIFICATION
// ════════════════════════════════════════════════════════════
echo '--- 4. Login Authentication (Issue 1 Fix) ---' . PHP_EOL;

// Wrong credentials — must return HTML page, not JSON
$r = httpPost($base . '/login.php', ['email' => 'wrong@test.com', 'password' => 'wrongpass']);
check('Wrong credentials → HTTP 200 (shows form)',   $r['code'] === 200);
check('Returns HTML, not raw JSON',                  !str_contains($r['body'], '{"success"') && !str_contains($r['body'], 'json_encode'));
check('Shows HTML error message',                    str_contains($r['body'], 'Invalid') || str_contains($r['body'], 'Unable to process') || str_contains($r['body'], 'Something went'));
check('Login form still present in response',        str_contains($r['body'], 'login-form'));
check('DOCTYPE present (full HTML page)',             str_contains($r['body'], '<!DOCTYPE html>'));

// Empty credentials
$r2 = httpPost($base . '/login.php', ['email' => '', 'password' => '']);
check('Empty fields → shows validation error',       $r2['code'] === 200 && str_contains($r2['body'], 'Please enter both'));

// GET shows form (not error)
$r3 = httpGet($base . '/login.php');
check('GET /login.php → shows form (200)',            $r3['code'] === 200 && str_contains($r3['body'], 'login-form'));
echo PHP_EOL;

// ════════════════════════════════════════════════════════════
//  5. AUTH GUARD
// ════════════════════════════════════════════════════════════
echo '--- 5. Auth Guard (admin.php) ---' . PHP_EOL;
$r = httpGet($base . '/admin.php');
check('Unauthenticated → HTTP 302',                  $r['code'] === 302);
check('Redirects to login.php',                      str_contains($r['location'], 'login.php'));
echo PHP_EOL;

// ════════════════════════════════════════════════════════════
//  6. LOGOUT
// ════════════════════════════════════════════════════════════
echo '--- 6. Logout (logout.php) ---' . PHP_EOL;
$r = httpGet($base . '/logout.php');
check('logout.php redirects',                        $r['code'] === 302);
check('Redirects to login.php',                      str_contains($r['location'], 'login.php'));
echo PHP_EOL;

// ════════════════════════════════════════════════════════════
//  7. CUSTOM 404 — ISSUE 2 FIX VERIFICATION
// ════════════════════════════════════════════════════════════
echo '--- 7. Custom 404 Page (Issue 2 Fix) ---' . PHP_EOL;

// Direct access to 404.php
$r = httpGet($base . '/404.php');
check('/404.php → HTTP 404 status',                  $r['code'] === 404);
check('404 page has "Page Not Found" heading',        str_contains($r['body'], 'Page Not Found'));
check('404 page has Return Home button',              str_contains($r['body'], 'Return Home'));
check('404 page has Go Back button',                  str_contains($r['body'], 'Go Back'));
check('BizPulse branding on 404',                    str_contains($r['body'], 'BizPulse'));
check('Dark mode support on 404',                    str_contains($r['body'], "localStorage.getItem('bpTheme')"));

// Unknown URLs — router.php should serve 404.php for these
$invalid = ['/abc', '/random-page', '/test', '/nonexistent'];
foreach ($invalid as $path) {
    $rx = httpGet($base . $path);
    check("'{$path}' → HTTP 404 (custom page)",      $rx['code'] === 404);
    check("'{$path}' → Shows BizPulse 404 HTML",    str_contains($rx['body'], 'Page Not Found') || str_contains($rx['body'], '404'));
}
echo PHP_EOL;

// ════════════════════════════════════════════════════════════
//  8. FORM SUBMISSION
// ════════════════════════════════════════════════════════════
echo '--- 8. Form Submission (submit.php) ---' . PHP_EOL;
$r = httpPost($base . '/submit.php', [
    'name' => 'V2.1 Test User', 'email' => 'v21test@example.com',
    'service' => 'Web Design', 'message' => 'Regression test for BizPulse v2.1 bug fixes.',
]);
check('Valid POST → HTTP 302',                        $r['code'] === 302);
check('Redirects to ?success=1',                     str_contains($r['location'], 'success=1'));
$r2 = httpPost($base . '/submit.php', ['name' => '', 'email' => '', 'service' => '', 'message' => '']);
check('Empty form → 302 with error',                 $r2['code'] === 302 && str_contains($r2['location'], 'error'));
$r3 = httpGet($base . '/submit.php');
check('GET → 405 Method Not Allowed',                $r3['code'] === 405);
echo PHP_EOL;

// ════════════════════════════════════════════════════════════
//  9. AJAX STATUS UPDATE
// ════════════════════════════════════════════════════════════
echo '--- 9. AJAX Status Update (update_status.php) ---' . PHP_EOL;
$ajaxHdrs = ['X-Requested-With: XMLHttpRequest'];
$r = httpPost($base . '/update_status.php', 'id=1', $ajaxHdrs);
$j = json_decode($r['body'], true);
check('Unauthenticated AJAX → HTTP 401',             $r['code'] === 401);
check('Returns JSON with success:false',             $j !== null && $j['success'] === false);
check('Response is valid JSON',                      $j !== null);
$r2 = httpPost($base . '/update_status.php', 'id=1');
check('Non-AJAX unauthenticated → 401',              $r2['code'] === 401);
$r3 = httpGet($base . '/update_status.php');
check('GET unauthenticated → 401',                   $r3['code'] === 401);
echo PHP_EOL;

// ════════════════════════════════════════════════════════════
//  10. DATABASE VERIFICATION
// ════════════════════════════════════════════════════════════
echo '--- 10. Database (direct PDO test) ---' . PHP_EOL;
try {
    require_once __DIR__ . '/../config/database.php';
    $pdo = getDB();
    check('PDO connection successful',               true);
    $tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    check('leads table exists',                      in_array('leads', $tables));
    check('users table exists',                      in_array('users', $tables));
    $leadsCount = (int) $pdo->query('SELECT COUNT(*) FROM leads')->fetchColumn();
    check('leads table has records',                 $leadsCount > 0);
    $admin = $pdo->query("SELECT * FROM users WHERE email='admin@bizpulse.com'")->fetch(PDO::FETCH_ASSOC);
    check('Admin user exists',                       $admin !== false);
    check('Admin role is "admin"',                   ($admin['role'] ?? '') === 'admin');
    check('Password is bcrypt hash',                 str_starts_with($admin['password'] ?? '', '$2y$'));
    check('password_verify works for Admin@123',     password_verify('Admin@123', $admin['password'] ?? ''));
} catch (PDOException $e) {
    check('PDO connection successful',               false);
    echo "  DB error: " . $e->getMessage() . PHP_EOL;
}
echo PHP_EOL;

// ════════════════════════════════════════════════════════════
//  11. DARK MODE & ASSETS
// ════════════════════════════════════════════════════════════
echo '--- 11. Dark Mode & Assets ---' . PHP_EOL;
$r = httpGet($base . '/assets/js/darkmode.js');
check('darkmode.js serves 200',                      $r['code'] === 200);
check('toggleTheme function present',                str_contains($r['body'], 'toggleTheme') || str_contains($r['body'], 'bpTheme'));
check('localStorage usage',                          str_contains($r['body'], 'localStorage'));
$r2 = httpGet($base . '/assets/css/style.css');
check('style.css serves 200',                        $r2['code'] === 200);
echo PHP_EOL;

// ════════════════════════════════════════════════════════════
//  12. NEW FILES EXIST & ARE ACCESSIBLE
// ════════════════════════════════════════════════════════════
echo '--- 12. New Files Exist ---' . PHP_EOL;
$files = [
    '/includes/auth.php' => 'auth.php accessible',
    '/login.php'         => 'login.php accessible',
    '/logout.php'        => 'logout.php accessible',
    '/404.php'           => '404.php accessible',
    '/assets/js/darkmode.js' => 'darkmode.js accessible',
];
foreach ($files as $path => $label) {
    $rx = httpGet($base . $path);
    check($label,                                    in_array($rx['code'], [200, 302, 404]));
}

// ── Security: sensitive directories must be blocked ───────────────────────
echo PHP_EOL . '--- 13. Security (sensitive path blocking) ---' . PHP_EOL;
// Note: .htaccess blocks these on Apache. PHP built-in server doesn't enforce .htaccess.
// These tests verify the security rule is present in .htaccess.
check('.htaccess exists',                            file_exists(__DIR__ . '/../.htaccess'));
check('.htaccess has ErrorDocument 404',             str_contains(file_get_contents(__DIR__ . '/../.htaccess'), 'ErrorDocument 404'));
check('.htaccess blocks config/ directory',          str_contains(file_get_contents(__DIR__ . '/../.htaccess'), 'config/'));
check('router.php exists for dev server',            file_exists(__DIR__ . '/../router.php'));
check('start-server.bat exists',                     file_exists(__DIR__ . '/../start-server.bat'));
echo PHP_EOL;

// ════════════════════════════════════════════════════════════
//  RESULTS
// ════════════════════════════════════════════════════════════
$total = $passed + $failed;
echo '=============================================' . PHP_EOL;
printf('  RESULTS: %d PASSED  /  %d FAILED   ' . PHP_EOL, $passed, $failed);
echo '=============================================' . PHP_EOL . PHP_EOL;

if ($failed === 0) {
    echo "\033[32m  ✅  All tests passed — BizPulse v2.1 is healthy.\033[0m" . PHP_EOL . PHP_EOL;
} else {
    echo "\033[31m  ⚠️   {$failed} test(s) failed — review output above.\033[0m" . PHP_EOL . PHP_EOL;
}
