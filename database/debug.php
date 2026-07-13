<?php
// Test MySQL passwords
foreach ([['root','root'], ['root','']] as [$u, $p]) {
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=bizpulse;charset=utf8mb4", $u, $p);
        echo "User=$u Pass=" . ($p === '' ? '(empty)' : $p) . ": OK" . PHP_EOL;
    } catch (PDOException $e) {
        echo "User=$u Pass=" . ($p === '' ? '(empty)' : $p) . ": FAIL - " . $e->getMessage() . PHP_EOL;
    }
}
