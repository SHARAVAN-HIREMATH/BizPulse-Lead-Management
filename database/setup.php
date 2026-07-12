<?php
/**
 * BizPulse — One-time database setup script
 * Run once: C:\xampp\php\php.exe database/setup.php
 * Safe to run multiple times (uses IF NOT EXISTS).
 */

$host     = 'localhost';
$username = 'root';
$password = 'root';
$charset  = 'utf8mb4';

echo "=== BizPulse Database Setup ===" . PHP_EOL;

try {
    // Connect WITHOUT selecting a database first
    $pdo = new PDO(
        "mysql:host={$host};charset={$charset}",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );

    echo "[1/4] Connected to MySQL " . $pdo->getAttribute(PDO::ATTR_SERVER_VERSION) . PHP_EOL;

    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS bizpulse
                CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE bizpulse");
    echo "[2/4] Database 'bizpulse' ready." . PHP_EOL;

    // Create leads table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS leads (
            id         INT          NOT NULL AUTO_INCREMENT,
            name       VARCHAR(100) NOT NULL,
            email      VARCHAR(150) NOT NULL,
            service    VARCHAR(100) NOT NULL,
            message    TEXT         NOT NULL,
            status     VARCHAR(50)  NOT NULL DEFAULT 'New',
            created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "[3/4] Table 'leads' ready." . PHP_EOL;

    // Seed data (only if table is empty)
    $count = (int) $pdo->query("SELECT COUNT(*) FROM leads")->fetchColumn();
    if ($count === 0) {
        $stmt = $pdo->prepare("
            INSERT INTO leads (name, email, service, message, status, created_at) VALUES
            (:n1, :e1, :s1, :m1, :st1, :d1),
            (:n2, :e2, :s2, :m2, :st2, :d2),
            (:n3, :e3, :s3, :m3, :st3, :d3),
            (:n4, :e4, :s4, :m4, :st4, :d4),
            (:n5, :e5, :s5, :m5, :st5, :d5)
        ");
        $stmt->execute([
            ':n1'=>'Alice Johnson',  ':e1'=>'alice@example.com',  ':s1'=>'Web Design',         ':m1'=>'Looking for a modern portfolio website for my photography studio.',      ':st1'=>'Contacted', ':d1'=> date('Y-m-d H:i:s', strtotime('-5 days')),
            ':n2'=>'Bob Martinez',   ':e2'=>'bob@example.com',    ':s2'=>'SEO Optimization',   ':m2'=>'We need to improve our Google ranking for local plumbing services.',      ':st2'=>'New',       ':d2'=> date('Y-m-d H:i:s', strtotime('-3 days')),
            ':n3'=>'Carol White',    ':e3'=>'carol@example.com',  ':s3'=>'Content Management', ':m3'=>'Our blog needs regular updates. Looking for a content management team.',  ':st3'=>'New',       ':d3'=> date('Y-m-d H:i:s', strtotime('-2 days')),
            ':n4'=>'David Brown',    ':e4'=>'david@example.com',  ':s4'=>'Web Design',         ':m4'=>'E-commerce store redesign. We sell handmade jewellery.',                  ':st4'=>'Contacted', ':d4'=> date('Y-m-d H:i:s', strtotime('-1 day')),
            ':n5'=>'Emma Davis',     ':e5'=>'emma@example.com',   ':s5'=>'SEO Optimization',   ':m5'=>'Startup looking for full digital marketing strategy and SEO setup.',      ':st5'=>'New',       ':d5'=> date('Y-m-d H:i:s'),
        ]);
        echo "[4/4] Seeded 5 sample leads." . PHP_EOL;
    } else {
        echo "[4/4] Table already has {$count} lead(s) — skipping seed." . PHP_EOL;
    }

    echo PHP_EOL . "✅ Setup complete! You can now run the app." . PHP_EOL;

} catch (PDOException $e) {
    echo "❌ ERROR: " . $e->getMessage() . PHP_EOL;
    exit(1);
}
