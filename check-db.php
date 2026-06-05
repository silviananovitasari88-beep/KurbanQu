<?php
$mysqli = new mysqli('127.0.0.1', 'root', '', 'kurbanqu');

echo "=== WARGA TABLE - LAST 5 RECORDS ===\n";
$result = $mysqli->query('SELECT * FROM warga ORDER BY created_at DESC LIMIT 5');
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo sprintf("No KK: %-15s | Nama: %-20s | Alamat: %-15s | Telp: %-12s | Created: %s\n",
            $row['no_kk'], 
            $row['nama_kk'],
            $row['alamat'] ?? '-',
            $row['no_telp'] ?? '-',
            $row['created_at']
        );
    }
    echo "\nTotal records in warga: " . $mysqli->query('SELECT COUNT(*) as cnt FROM warga')->fetch_assoc()['cnt'] . "\n";
} else {
    echo "No data found or query failed: " . $mysqli->error . "\n";
}

echo "\n=== WARGA_UPLOADS TABLE - LAST 3 RECORDS ===\n";
$result2 = $mysqli->query('SELECT * FROM warga_uploads ORDER BY uploaded_at DESC LIMIT 3');
if ($result2 && $result2->num_rows > 0) {
    while ($row = $result2->fetch_assoc()) {
        echo sprintf("ID: %d | File: %s | Jumlah: %d | Status: %s | Error: %s\n",
            $row['id'],
            $row['filename'],
            $row['jumlah_baris'],
            $row['status'],
            $row['error_message'] ?? 'none'
        );
    }
} else {
    echo "No upload records\n";
}

$mysqli->close();
?>
