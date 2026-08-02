<?php
/**
 * Script Hapus Migration Tidak Terpakai
 */
$filesToDelete = [
    __DIR__ . '/database/migrations/2026_06_17_230003_create_kurbanqu_tables.php',
    __DIR__ . '/database/migrations/2026_08_01_000000_cleanup_legacy_tables.php',
];

foreach ($filesToDelete as $file) {
    if (file_exists($file)) {
        if (unlink($file)) {
            echo "Berhasil menghapus: " . basename($file) . "\n";
        } else {
            echo "Gagal menghapus: " . basename($file) . "\n";
        }
    } else {
        echo "File tidak ditemukan (sudah terhapus): " . basename($file) . "\n";
    }
}
