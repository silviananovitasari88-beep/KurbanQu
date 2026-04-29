<?php
/**
 * Test booking endpoint dengan CSRF handling
 */
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';

// Test 1: Cek apakah route accessible
echo "=== TEST BOOKING ENDPOINT ===\n\n";

$testData = [
    'fullName' => 'Budi Santoso',
    'email' => 'budi@test.com',
    'phone' => '081223288620',
    'roomNumber' => '105-01'
];

// Simulate POST request dengan simulating HTTP
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['REQUEST_URI'] = '/bookings/create';
$_SERVER['CONTENT_TYPE'] = 'application/json';

// Test dengan cURL ke localhost
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => 'http://localhost/aulia_kost/bookings/create',
    CURLOPT_POST => 1,
    CURLOPT_POSTFIELDS => json_encode($testData),
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
    ],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_VERBOSE => true,
]);

echo "Testing endpoint: http://localhost/aulia_kost/bookings/create\n";
echo "Request data: " . json_encode($testData) . "\n\n";

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

if ($error) {
    echo "❌ CURL Error: $error\n";
} else {
    echo "HTTP Status: $httpCode\n";
    echo "Response:\n";
    echo $response . "\n\n";
    
    // Try parse JSON
    $decoded = json_decode($response, true);
    if ($decoded) {
        echo "✓ Valid JSON\n";
        echo "✓ success: " . ($decoded['success'] ? 'true' : 'false') . "\n";
        if (isset($decoded['booking']['id'])) {
            echo "✓ booking.id: " . $decoded['booking']['id'] . "\n";
        } else {
            echo "✗ booking.id tidak ada\n";
        }
    } else {
        echo "✗ Invalid JSON response\n";
        echo "JSON Error: " . json_last_error_msg() . "\n";
    }
}

curl_close($ch);
