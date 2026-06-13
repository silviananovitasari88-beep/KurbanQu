<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=kurbanqu;charset=utf8mb4', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $tables = ['warga', 'distribusi', 'QR', 'tracking'];
    foreach ($tables as $table) {
        echo "### Table: $table\n";
        try {
            $stmt = $pdo->query("DESCRIBE `$table`");
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($columns as $col) {
                echo "  - {$col['Field']} ({$col['Type']}) Null:{$col['Null']} Key:{$col['Key']} Default:{$col['Default']}\n";
            }
            $stmtCount = $pdo->query("SELECT COUNT(*) as cnt FROM `$table`");
            $count = $stmtCount->fetch(PDO::FETCH_ASSOC)['cnt'];
            echo "  - Total rows: $count\n";
        } catch (PDOException $e) {
            echo "  - Error: " . $e->getMessage() . "\n";
        }
        echo "\n";
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
