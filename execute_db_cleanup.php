<?php
/**
 * Database Direct Cleanup Script for KurbanQu (phpMyAdmin / MySQL)
 * Jalankan script ini via browser atau terminal untuk menghapus kolom & tabel langsung di database MySQL.
 */

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Database Cleanup KurbanQu</title>";
echo "<style>body { font-family: monospace; padding: 20px; background: #1e1e1e; color: #d4d4d4; } .success { color: #4ec9b0; } .error { color: #f48771; } .info { color: #ce9178; }</style></head><body>";
echo "<h2>🔧 Database Cleanup Direct Execution (phpMyAdmin / MySQL)</h2>";
echo "<pre>";

try {
    // Koneksi PDO ke MySQL lokal
    $pdo = new PDO(
        'mysql:host=127.0.0.1;dbname=kurbanqu;charset=utf8mb4',
        'root',
        ''
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<span class='info'>✓ Database connection 'kurbanqu' OK</span>\n\n";

    // 1. Hapus kolom last_login_at dan is_online dari tabel warga
    echo "⏳ 1. Menghapus kolom 'last_login_at' & 'is_online' dari tabel warga...\n";
    $wargaColumns = ['last_login_at', 'is_online'];
    foreach ($wargaColumns as $col) {
        try {
            $pdo->exec("ALTER TABLE `warga` DROP COLUMN `$col`");
            echo "  <span class='success'>✅ Kolom '$col' berhasil dihapus dari tabel warga</span>\n";
        } catch (PDOException $e) {
            echo "  <span class='info'>ℹ️ Kolom '$col' tidak ditemukan / sudah dihapus</span>\n";
        }
    }

    // 2. Hapus kolom email dan remember_token dari tabel users
    echo "\n⏳ 2. Menghapus kolom 'email' & 'remember_token' dari tabel users...\n";
    $usersColumns = ['email', 'remember_token'];
    foreach ($usersColumns as $col) {
        try {
            $pdo->exec("ALTER TABLE `users` DROP COLUMN `$col`");
            echo "  <span class='success'>✅ Kolom '$col' berhasil dihapus dari tabel users</span>\n";
        } catch (PDOException $e) {
            echo "  <span class='info'>ℹ️ Kolom '$col' tidak ditemukan / sudah dihapus</span>\n";
        }
    }

    // 3. Hapus kolom tracking_id_tracking dari tabel hewan
    echo "\n⏳ 3. Menghapus kolom 'tracking_id_tracking' dari tabel hewan...\n";
    try {
        $pdo->exec("ALTER TABLE `hewan` DROP COLUMN `tracking_id_tracking`");
        echo "  <span class='success'>✅ Kolom 'tracking_id_tracking' berhasil dihapus dari tabel hewan</span>\n";
    } catch (PDOException $e) {
        echo "  <span class='info'>ℹ️ Kolom 'tracking_id_tracking' tidak ditemukan / sudah dihapus</span>\n";
    }

    // 4. Hapus tabel tracking & warga_uploads
    echo "\n⏳ 4. Menghapus tabel 'tracking' & 'warga_uploads'...\n";
    try {
        $pdo->exec("DROP TABLE IF EXISTS `tracking`");
        echo "  <span class='success'>✅ Tabel 'tracking' berhasil dihapus</span>\n";
    } catch (PDOException $e) {
        echo "  <span class='error'>❌ Gagal menghapus tabel tracking: " . $e->getMessage() . "</span>\n";
    }

    try {
        $pdo->exec("DROP TABLE IF EXISTS `warga_uploads`");
        echo "  <span class='success'>✅ Tabel 'warga_uploads' berhasil dihapus</span>\n";
    } catch (PDOException $e) {
        echo "  <span class='error'>❌ Gagal menghapus tabel warga_uploads: " . $e->getMessage() . "</span>\n";
    }

    echo "\n<span class='success'>✅ ✅ ✅ PEMBERSIHAN DATABASE MYSQL BERHASIL DILAKUKAN! ✅ ✅ ✅</span>\n";

} catch (PDOException $e) {
    echo "\n<span class='error'>❌ DATABASE ERROR:</span>\n";
    echo $e->getMessage() . "\n";
}

echo "</pre>";
echo "</body></html>";
