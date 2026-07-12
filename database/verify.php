<?php
/**
 * BizPulse — Full Verification Test Suite
 */

echo '========================================' . PHP_EOL;
echo '  BIZPULSE — FULL VERIFICATION REPORT  ' . PHP_EOL;
echo '========================================' . PHP_EOL . PHP_EOL;

$base = 'http://localhost:8080';
$pass = 0;
$fail = 0;

function check(string $label, bool $ok): void {
    global $pass, $fail;
    if ($ok) { echo '[PASS] ' . $label . PHP_EOL; $pass++; }
    else      { echo '[FAIL] ' . $label . PHP_EOL; $fail++; }
}

function httpGet(string $url): array {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    $body = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ['code' => $code, 'body' => $body];
}

function httpPost(string $url, string|array $fields, array $headers = []): array {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($fields) ? http_build_query($fields) : $fields);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    if ($headers) curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $body = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $loc  = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
    curl_close($ch);
    return ['code' => $code, 'body' => $body, 'location' => $loc];
}

// ── Landing Page ────────────────────────────────────────────────────────────
echo '--- Landing Page (index.php) ---' . PHP_EOL;
$r = httpGet($base . '/');
check('HTTP 200 OK',                            $r['code'] === 200);
check('Brand name BizPulse present',            str_contains($r['body'], 'BizPulse'));
check('Hero tagline present',                   str_contains($r['body'], 'Grow Digitally'));
check('Service: Web Design present',            str_contains($r['body'], 'Web Design'));
check('Service: SEO Optimization present',      str_contains($r['body'], 'SEO Optimization'));
check('Service: Content Management present',    str_contains($r['body'], 'Content Management'));
check('Contact form rendered',                  str_contains($r['body'], 'id="contact-form"'));
check('Tailwind CDN loaded',                    str_contains($r['body'], 'cdn.tailwindcss.com'));
echo PHP_EOL;

// ── Form Submission ─────────────────────────────────────────────────────────
echo '--- Form Submission (submit.php) ---' . PHP_EOL;
$r = httpPost($base . '/submit.php', [
    'name' => 'CLI Test User', 'email' => 'clitest@example.com',
    'service' => 'Web Design',  'message' => 'Automated test submission for verification.',
    'form_token' => 'test'
]);
check('Successful POST → HTTP 302',             $r['code'] === 302);
check('Redirects to index.php?success=1',       str_contains($r['location'], 'success=1'));

$r2 = httpPost($base . '/submit.php', ['name'=>'','email'=>'','service'=>'','message'=>'']);
check('Rejects empty form (redirects w/ error)', $r2['code'] === 302 && str_contains($r2['location'], 'error'));

$r3 = httpPost($base . '/submit.php', ['name'=>'Test','email'=>'not-an-email','service'=>'Web Design','message'=>'Hello there.']);
check('Rejects invalid email',                  $r3['code'] === 302 && str_contains($r3['location'], 'error'));

$r4 = httpPost($base . '/submit.php', ['name'=>'Test','email'=>'test@test.com','service'=>'HACK INJECTION','message'=>'Test.']);
check('Rejects non-whitelisted service',        $r4['code'] === 302 && str_contains($r4['location'], 'error'));

$r5 = httpGet($base . '/submit.php');
check('Rejects GET request → HTTP 405',         $r5['code'] === 405);
echo PHP_EOL;

// ── Admin Dashboard ─────────────────────────────────────────────────────────
echo '--- Admin Dashboard (admin.php) ---' . PHP_EOL;
$r = httpGet($base . '/admin.php');
check('HTTP 200 OK',                            $r['code'] === 200);
check('Dashboard heading present',              str_contains($r['body'], 'Lead Dashboard'));
check('Total Leads stat card present',          str_contains($r['body'], 'Total Leads'));
check('New Leads stat card present',            str_contains($r['body'], 'New Leads'));
check('Contacted stat card present',            str_contains($r['body'], 'Contacted'));
check('Leads table rendered',                   str_contains($r['body'], 'leads-table'));
check('Lead rows present',                      str_contains($r['body'], 'lead-row'));
check('AJAX function wired up',                 str_contains($r['body'], 'updateLeadStatus'));
check('admin.js included',                      str_contains($r['body'], 'admin.js'));
check('Live search input present',              str_contains($r['body'], 'lead-search'));
echo PHP_EOL;

// ── AJAX Status Update ──────────────────────────────────────────────────────
echo '--- AJAX Status Update (update_status.php) ---' . PHP_EOL;
$ajaxHdrs = ['X-Requested-With: XMLHttpRequest', 'Content-Type: application/x-www-form-urlencoded'];

$r = httpPost($base . '/update_status.php', 'id=1', $ajaxHdrs);
$json = json_decode($r['body'], true);
check('HTTP 200 on valid AJAX POST',            $r['code'] === 200);
check('Returns valid JSON',                     $json !== null);
check('JSON has "success" key',                 isset($json['success']));
check('success is boolean true',                $json['success'] === true);

$r2 = httpPost($base . '/update_status.php', 'id=1');    // no AJAX header
check('Blocks non-AJAX → HTTP 403',             $r2['code'] === 403);

$r3 = httpGet($base . '/update_status.php');
check('Rejects GET → HTTP 405',                 $r3['code'] === 405);

$r4 = httpPost($base . '/update_status.php', 'id=abc', $ajaxHdrs);
$j4 = json_decode($r4['body'], true);
check('Rejects non-integer ID → HTTP 422',      $r4['code'] === 422 && $j4['success'] === false);

$r5 = httpPost($base . '/update_status.php', 'id=-99', $ajaxHdrs);
$j5 = json_decode($r5['body'], true);
check('Rejects negative ID → HTTP 422',         $r5['code'] === 422 && $j5['success'] === false);
echo PHP_EOL;

// ── Database ─────────────────────────────────────────────────────────────────
echo '--- Database (MySQL via PDO) ---' . PHP_EOL;
try {
    $pdo = new PDO('mysql:host=localhost;dbname=bizpulse;charset=utf8mb4', 'root', 'root', [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    check('PDO connection to bizpulse DB',      true);
    check('MySQL 8.x server',                   version_compare($pdo->getAttribute(PDO::ATTR_SERVER_VERSION), '8', '>='));

    $count = (int) $pdo->query('SELECT COUNT(*) FROM leads')->fetchColumn();
    check("Leads table has records ({$count})", $count > 0);

    $cols = $pdo->query('DESCRIBE leads')->fetchAll(PDO::FETCH_COLUMN);
    foreach (['id','name','email','service','message','status','created_at'] as $col) {
        check("Column '{$col}' exists",         in_array($col, $cols));
    }

    // Verify default status is 'New' in schema
    $def = $pdo->query("SHOW COLUMNS FROM leads LIKE 'status'")->fetch();
    check("status default is 'New'",            $def['Default'] === 'New');

} catch (PDOException $e) {
    check('Database connection', false);
    echo '  Error: ' . $e->getMessage() . PHP_EOL;
}
echo PHP_EOL;

// ── Summary ──────────────────────────────────────────────────────────────────
echo '========================================' . PHP_EOL;
echo "  RESULTS: {$pass} PASSED  /  {$fail} FAILED   " . PHP_EOL;
echo '========================================' . PHP_EOL;
if ($fail === 0) {
    echo PHP_EOL . '  ALL TESTS PASSED. App is production-ready!' . PHP_EOL;
}
