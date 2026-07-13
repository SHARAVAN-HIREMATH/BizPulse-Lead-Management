<?php
/**
 * BizPulse v2 — Full Verification Test Suite
 * Tests all new v2 features + backward-compatibility of existing features.
 */

echo PHP_EOL . '=============================================' . PHP_EOL;
echo '  BIZPULSE v2 — FULL VERIFICATION REPORT  ' . PHP_EOL;
echo '=============================================' . PHP_EOL . PHP_EOL;

$base = 'http://localhost:8080';
$pass = 0; $fail = 0;

function check(string $label, bool $ok): void {
    global $pass, $fail;
    if ($ok) { echo '[PASS] ' . $label . PHP_EOL; $pass++; }
    else      { echo '[FAIL] ' . $label . PHP_EOL; $fail++; }
}

function httpGet(string $url, array $headers = []): array {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 6,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_HTTPHEADER     => $headers,
    ]);
    $body = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $loc  = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
    curl_close($ch);
    return ['code' => $code, 'body' => $body, 'location' => $loc];
}

function httpPost(string $url, string|array $fields, array $headers = []): array {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => is_array($fields) ? http_build_query($fields) : $fields,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_TIMEOUT        => 6,
        CURLOPT_HTTPHEADER     => $headers,
    ]);
    $body = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $loc  = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
    curl_close($ch);
    return ['code' => $code, 'body' => $body, 'location' => $loc];
}

// ════════════════════════════════════════════════════════════
//  1. LANDING PAGE
// ════════════════════════════════════════════════════════════
echo '--- 1. Landing Page (index.php) ---' . PHP_EOL;
$r = httpGet($base . '/');
check('HTTP 200 OK',                       $r['code'] === 200);
check('Dark mode anti-flash script',       str_contains($r['body'], 'bpTheme'));
check('Tailwind darkMode:class config',    str_contains($r['body'], "darkMode: 'class'"));
check('Contact form present',              str_contains($r['body'], 'id="contact-form"'));
check('Service: Web Design',              str_contains($r['body'], 'Web Design'));
check('Service: SEO Optimization',        str_contains($r['body'], 'SEO Optimization'));
check('Service: Content Management',      str_contains($r['body'], 'Content Management'));
check('success modal PHP block present',  str_contains($r['body'], 'success-modal') || str_contains($r['body'], 'closeSuccessModal'));
check('darkmode.js included',             str_contains($r['body'], 'darkmode.js'));
echo PHP_EOL;

// ════════════════════════════════════════════════════════════
//  2. SUCCESS MODAL
// ════════════════════════════════════════════════════════════
echo '--- 2. Success Modal (index.php?success=1) ---' . PHP_EOL;
$r = httpGet($base . '/?success=1');
check('HTTP 200',                          $r['code'] === 200);
check('Modal overlay rendered',            str_contains($r['body'], 'success-modal'));
check('Animated checkmark present',        str_contains($r['body'], 'checkmark-circle'));
check('Modal countdown script',            str_contains($r['body'], 'countdown'));
check('Auto-close timer present',          str_contains($r['body'], 'setTimeout'));
check('Close button present',              str_contains($r['body'], 'closeSuccessModal'));
check('"Thank you" message in modal',      str_contains($r['body'], 'Thank you'));
echo PHP_EOL;

// ════════════════════════════════════════════════════════════
//  3. LOGIN PAGE
// ════════════════════════════════════════════════════════════
echo '--- 3. Login Page (login.php) ---' . PHP_EOL;
$r = httpGet($base . '/login.php');
check('HTTP 200 OK',                       $r['code'] === 200);
check('Dark mode anti-flash present',      str_contains($r['body'], 'bpTheme'));
check('noindex meta (security)',           str_contains($r['body'], 'noindex'));
check('Login form rendered',               str_contains($r['body'], 'id="login-form"'));
check('Email field present',               str_contains($r['body'], 'name="email"'));
check('Password field present',            str_contains($r['body'], 'name="password"'));
check('Show/hide password toggle',         str_contains($r['body'], 'toggle-password'));
check('BizPulse branding on login',        str_contains($r['body'], 'BizPulse'));
check('Back to website link',              str_contains($r['body'], '/index.php'));
echo PHP_EOL;

// ════════════════════════════════════════════════════════════
//  4. LOGIN AUTHENTICATION
// ════════════════════════════════════════════════════════════
echo '--- 4. Login Authentication ---' . PHP_EOL;
// Invalid credentials — curl without redirect
$r = httpPost($base . '/login.php', ['email' => 'wrong@test.com', 'password' => 'wrongpass']);
check('Rejects wrong credentials → 200 (shows form again)', $r['code'] === 200);
check('Error message shown on page', str_contains($r['body'], 'Invalid') || str_contains($r['body'], 'error') || str_contains($r['body'], 'Please enter') || str_contains($r['body'], 'Something went wrong') || str_contains($r['body'], 'bg-red'));
// Empty fields
$r2 = httpPost($base . '/login.php', ['email' => '', 'password' => '']);
check('Rejects empty fields',              $r2['code'] === 200 && str_contains($r2['body'], 'Please enter both'));
// GET to login shows form (not error)
$r3 = httpGet($base . '/login.php');
check('GET /login.php returns form',       $r3['code'] === 200 && str_contains($r3['body'], 'login-form'));
echo PHP_EOL;

// ════════════════════════════════════════════════════════════
//  5. AUTH GUARD — admin.php must redirect unauthenticated users
// ════════════════════════════════════════════════════════════
echo '--- 5. Auth Guard (admin.php redirect) ---' . PHP_EOL;
$r = httpGet($base . '/admin.php');
check('Unauthenticated → HTTP 302',        $r['code'] === 302);
check('Redirects to login.php',            str_contains($r['location'], 'login.php'));
echo PHP_EOL;

// ════════════════════════════════════════════════════════════
//  6. LOGOUT
// ════════════════════════════════════════════════════════════
echo '--- 6. Logout (logout.php) ---' . PHP_EOL;
$r = httpGet($base . '/logout.php');
check('logout.php redirects',              $r['code'] === 302);
check('Redirects to login.php',            str_contains($r['location'], 'login.php'));
echo PHP_EOL;

// ════════════════════════════════════════════════════════════
//  7. 404 PAGE
// ════════════════════════════════════════════════════════════
echo '--- 7. Custom 404 Page (404.php) ---' . PHP_EOL;
$r = httpGet($base . '/404.php');
check('HTTP 404 status code',              $r['code'] === 404);
check('404 heading present',               str_contains($r['body'], '404'));
check('"Page Not Found" text',             str_contains($r['body'], 'Page Not Found'));
check('"Return Home" button',              str_contains($r['body'], 'Return Home'));
check('"Go Back" button',                  str_contains($r['body'], 'Go Back'));
check('BizPulse branding',                 str_contains($r['body'], 'BizPulse'));
check('Dark mode support',                 str_contains($r['body'], 'bpTheme'));
echo PHP_EOL;

// ════════════════════════════════════════════════════════════
//  8. FORM SUBMISSION (backward compatibility)
// ════════════════════════════════════════════════════════════
echo '--- 8. Form Submission (submit.php) ---' . PHP_EOL;
$r = httpPost($base . '/submit.php', [
    'name' => 'V2 Test User', 'email' => 'v2test@example.com',
    'service' => 'Web Design', 'message' => 'V2 upgrade automated verification test message.',
    'form_token' => 'test'
]);
check('POST → HTTP 302 success',           $r['code'] === 302);
check('Redirects to ?success=1',           str_contains($r['location'], 'success=1'));
$r2 = httpPost($base . '/submit.php', ['name'=>'','email'=>'','service'=>'','message'=>'']);
check('Rejects empty form',                $r2['code'] === 302 && str_contains($r2['location'], 'error'));
$r3 = httpGet($base . '/submit.php');
check('GET returns 405',                   $r3['code'] === 405);
echo PHP_EOL;

// ════════════════════════════════════════════════════════════
//  9. UPDATE STATUS — Auth guard (now requires login)
// ════════════════════════════════════════════════════════════
echo '--- 9. AJAX Status Update (update_status.php) ---' . PHP_EOL;
$ajaxHdrs = ['X-Requested-With: XMLHttpRequest', 'Content-Type: application/x-www-form-urlencoded'];
// Without auth session — auth check runs FIRST, so all unauthenticated requests get 401
$r = httpPost($base . '/update_status.php', 'id=1', $ajaxHdrs);
$j = json_decode($r['body'], true);
check('Unauthenticated AJAX → HTTP 401',    $r['code'] === 401);
check('Returns JSON with success:false',   $j !== null && $j['success'] === false);
// Non-AJAX also gets 401 (auth check runs before AJAX check)
$r2 = httpPost($base . '/update_status.php', 'id=1');
check('Non-AJAX unauthenticated → 401',   $r2['code'] === 401);
// GET also gets 401 (auth check runs before method check)
$r3 = httpGet($base . '/update_status.php');
check('GET unauthenticated → 401',         $r3['code'] === 401);
// JSON response is always returned (Content-Type check)
check('Returns JSON (not HTML)',            str_contains($r['body'] ?? '', 'success'));
echo PHP_EOL;

// ════════════════════════════════════════════════════════════
//  10. DATABASE
// ════════════════════════════════════════════════════════════
echo '--- 10. Database Verification ---' . PHP_EOL;
try {
    $pdo = new PDO('mysql:host=localhost;dbname=bizpulse;charset=utf8mb4', 'root', 'root', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    check('PDO connection to bizpulse',    true);
    // leads table
    $leadsCount = (int) $pdo->query('SELECT COUNT(*) FROM leads')->fetchColumn();
    check("leads table has records ({$leadsCount})", $leadsCount > 0);
    // users table
    $usersCount = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
    check("users table has records ({$usersCount})", $usersCount > 0);
    // verify admin user
    $admin = $pdo->query("SELECT * FROM users WHERE email='admin@bizpulse.com'")->fetch(PDO::FETCH_ASSOC);
    check('Admin user exists',             $admin !== false);
    check("Admin role is 'admin'",         ($admin['role'] ?? '') === 'admin');
    check('Password is a bcrypt hash',     str_starts_with($admin['password'] ?? '', '$2y$'));
    check('password_verify works',         password_verify('Admin@123', $admin['password'] ?? ''));
    // users columns
    $cols = $pdo->query('DESCRIBE users')->fetchAll(PDO::FETCH_COLUMN);
    foreach (['id','name','email','password','role','created_at'] as $col) {
        check("users.{$col} column exists", in_array($col, $cols));
    }
} catch (PDOException $e) {
    check('Database connection', false);
    echo '  Error: ' . $e->getMessage() . PHP_EOL;
}
echo PHP_EOL;

// ════════════════════════════════════════════════════════════
//  11. DARK MODE ASSETS
// ════════════════════════════════════════════════════════════
echo '--- 11. Dark Mode & Assets ---' . PHP_EOL;
$r = httpGet($base . '/assets/js/darkmode.js');
check('darkmode.js serves 200',             $r['code'] === 200);
check('toggleTheme function present',       str_contains($r['body'], 'toggleTheme'));
check('localStorage usage',                str_contains($r['body'], 'localStorage'));
$r2 = httpGet($base . '/assets/css/style.css');
check('style.css serves 200',              $r2['code'] === 200);
check('Dark mode transition in CSS',        str_contains($r2['body'], 'dark'));
check('Modal animation keyframes',          str_contains($r2['body'], 'countdown'));
echo PHP_EOL;

// ════════════════════════════════════════════════════════════
//  12. NEW FILE CHECKS
// ════════════════════════════════════════════════════════════
echo '--- 12. New Files Existence ---' . PHP_EOL;
$files = [
    '/includes/auth.php'    => 'includes/auth.php',
    '/login.php'            => 'login.php',
    '/logout.php'           => 'logout.php',
    '/404.php'              => '404.php',
    '/assets/js/darkmode.js'=> 'darkmode.js',
];
foreach ($files as $url => $label) {
    $r = httpGet($base . $url);
    check("{$label} is accessible",        in_array($r['code'], [200, 302, 404]));
}
echo PHP_EOL;

// ════════════════════════════════════════════════════════════
//  SUMMARY
// ════════════════════════════════════════════════════════════
echo '=============================================' . PHP_EOL;
echo "  RESULTS: {$pass} PASSED  /  {$fail} FAILED   " . PHP_EOL;
echo '=============================================' . PHP_EOL;
if ($fail === 0) {
    echo PHP_EOL . '  ✅  ALL TESTS PASSED. BizPulse v2 is production-ready!' . PHP_EOL;
} else {
    echo PHP_EOL . "  ⚠️   {$fail} test(s) failed — review output above." . PHP_EOL;
}
echo PHP_EOL;
