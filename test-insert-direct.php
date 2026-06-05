<?php
// Direct test insert ke database
require 'vendor/autoload.php';
require 'bootstrap/app.php';

use App\Models\Warga;
use Illuminate\Support\Facades\DB;

try {
    echo "Starting test insert...\n";
    
    // Test 1: Raw SQL insert
    echo "\n=== Test 1: Raw SQL Insert ===\n";
    $now = date('Y-m-d H:i:s');
    $result = DB::table('warga')->insert([
        'no_kk'       => '1111111111',
        'nama_kk'     => 'Test Nama 1',
        'alamat'      => 'Jl Test 1',
        'no_telp'     => '081111111',
        'id_penerima' => 1,
        'created_at'  => $now,
        'updated_at'  => $now,
    ]);
    echo "Result: " . ($result ? 'SUCCESS' : 'FAILED') . "\n";
    
    // Verify insert
    $count = DB::table('warga')->count();
    echo "Total records in warga: $count\n";
    
    // Show last 3 records
    $records = DB::table('warga')->orderBy('no_kk', 'desc')->limit(3)->get();
    echo "\nLast 3 records:\n";
    foreach ($records as $rec) {
        echo "  - NoKK: {$rec->no_kk}, Nama: {$rec->nama_kk}, Created: {$rec->created_at}\n";
    }
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
?>
