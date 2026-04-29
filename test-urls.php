<?php
echo "=== TESTING DIFFERENT URL PATHS ===\n\n";

$testData = json_encode([
    'fullName' => 'Test User',
    'email' => 'test@test.com',
    'phone' => '081234567890',
    'roomNumber' => '105-01'
]);

$urls = [
    'http://localhost/aulia_kost/bookings/create',
    'http://localhost/aulia_kost/public/index.php/bookings/create',
    'http://localhost/aulia_kost/public/bookings/create',
];

foreach ($urls as $url) {
    echo "Testing: $url\n";
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => $testData,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 5,
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    echo "  Status: $httpCode\n";
    
    if ($error) {
        echo "  Error: $error\n";
    } else {
        $decoded = json_decode($response, true);
        if ($decoded && isset($decoded['success'])) {
            echo "  ✓ SUCCESS! Booking ID: " . $decoded['booking']['id'] . "\n";
        } else {
            echo "  Response: " . substr($response, 0, 100) . "...\n";
        }
    }
    
    echo "\n";
}
