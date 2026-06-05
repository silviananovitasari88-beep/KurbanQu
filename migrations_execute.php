<?php
/**
 * Database Migration Script - Direct PDO Connection
 * Jalankan script ini untuk execute tahap 1 & 2 migrations
 */

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Database Migration</title>";
echo "<style>body { font-family: monospace; padding: 20px; background: #1e1e1e; color: #d4d4d4; } .success { color: #4ec9b0; } .error { color: #f48771; } .info { color: #ce9178; }</style></head><body>";
echo "<h2>🔧 Database Migration - TAHAP 1 & 2</h2>";
echo "<pre>";

try {
    // Direct PDO connection (XAMPP default)
    $pdo = new PDO(
        'mysql:host=127.0.0.1;dbname=kurbanqu;charset=utf8mb4',
        'root',
        ''
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<span class='info'>✓ Database connection OK</span>\n\n";
    
    // TAHAP 1: ALTER TABLE warga
    echo "⏳ TAHAP 1: Menambah kolom ke table warga...\n";
    
    $sqls = [
        "ALTER TABLE `warga` ADD COLUMN IF NOT EXISTS `alamat` VARCHAR(255) NULL AFTER `nama_kk`",
        "ALTER TABLE `warga` ADD COLUMN IF NOT EXISTS `no_telp` VARCHAR(20) NULL AFTER `alamat`",
        "ALTER TABLE `warga` ADD COLUMN IF NOT EXISTS `id_penerima` INT UNIQUE NULL AFTER `no_telp`",
        "ALTER TABLE `warga` ADD COLUMN IF NOT EXISTS `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
        "ALTER TABLE `warga` ADD COLUMN IF NOT EXISTS `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"
    ];
    
    foreach ($sqls as $sql) {
        try {
            $pdo->exec($sql);
            echo "  <span class='success'>✅</span> " . substr($sql, 0, 60) . "...\n";
        } catch (PDOException $e) {
            // Ignore "duplicate column" errors
            if (strpos($e->getMessage(), 'Duplicate') === false) {
                throw $e;
            }
            echo "  <span class='info'>ℹ️</span> Kolom sudah ada (skip)\n";
        }
    }
    
    echo "\n<span class='success'>✅ TAHAP 1 SELESAI!</span>\n\n";
    
    // TAHAP 2: CREATE TABLE warga_uploads
    echo "⏳ TAHAP 2: Membuat table warga_uploads...\n";
    
    $sql = "CREATE TABLE IF NOT EXISTS `warga_uploads` (
        `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `filename` VARCHAR(255) NOT NULL,
        `jumlah_baris` INT NOT NULL,
        `mode` ENUM('append', 'replace') DEFAULT 'append',
        `admin_id` INT,
        `status` ENUM('pending', 'success', 'failed') DEFAULT 'pending',
        `error_message` TEXT NULL,
        `uploaded_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `processed_at` TIMESTAMP NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci";
    
    $pdo->exec($sql);
    echo "  <span class='success'>✅</span> Table 'warga_uploads' berhasil dibuat\n";
    echo "\n<span class='success'>✅ TAHAP 2 SELESAI!</span>\n\n";
    
    // VERIFIKASI
    echo "📊 VERIFIKASI:\n";
    
    $stmt = $pdo->query("SHOW COLUMNS FROM warga");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "  Kolom di table warga: <span class='success'>" . count($columns) . " kolom</span>\n";
    foreach ($columns as $col) {
        echo "    • {$col['Field']} ({$col['Type']})\n";
    }
    
    echo "\n";
    
    $stmt = $pdo->query("SHOW TABLES LIKE 'warga_uploads'");
    $exists = $stmt->rowCount() > 0;
    if ($exists) {
        echo "  <span class='success'>✅ Table warga_uploads sudah ada</span>\n";
    } else {
        echo "  <span class='error'>❌ Table warga_uploads tidak ada</span>\n";
    }
    
    echo "\n<span class='success'>✅ ✅ ✅ SEMUA TAHAP BERHASIL! ✅ ✅ ✅</span>\n";
    echo "\n➡️  NEXT: Lanjut ke LANGKAH 2 (Verify Database)\n";
    
} catch (PDOException $e) {
    echo "\n<span class='error'>❌ DATABASE ERROR:</span>\n";
    echo $e->getMessage() . "\n\n";
    echo "<span class='info'>💡 SOLUSI:</span>\n";
    echo "1. Pastikan MySQL sudah running\n";
    echo "2. Pastikan database 'kurbanqu' sudah ada\n";
    echo "3. Coba di phpMyAdmin manual (lihat IMPLEMENTATION_INSTRUCTIONS.md)\n";
} catch (Exception $e) {
    echo "\n<span class='error'>❌ ERROR:</span>\n";
    echo $e->getMessage() . "\n";
}

echo "</pre>";
echo "<hr>";
echo "<p><strong>📌 Status:</strong> Lihat hasil di atas. Kalau sukses ✅, lanjut ke LANGKAH 2!</p>";
echo "</body></html>";
?>
