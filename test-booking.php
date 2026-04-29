<?php
/**
 * Test script untuk testing JSON response dari booking endpoint
 * Jalankan: php test-booking.php
 */

// Setup Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

// Simulate a POST request untuk testing
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['REQUEST_URI'] = '/bookings/create';
$_SERVER['HTTP_CONTENT_TYPE'] = 'application/json';

// Test data
$testData = [
    'fullName' => 'Test User',
    'email' => 'test@example.com',
    'phone' => '081223288620',
    'roomNumber' => 1,
];

echo "=== Testing Booking Endpoint ===\n\n";
echo "Test Data:\n";
echo json_encode($testData, JSON_PRETTY_PRINT) . "\n\n";

// Make actual HTTP request using curl
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost/aulia_kost/bookings/create');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'X-CSRF-TOKEN: test-token'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status: $httpCode\n";
echo "Response:\n";

// Try to parse JSON
if ($response) {
    $decoded = json_decode($response, true);
    if ($decoded) {
        echo json_encode($decoded, JSON_PRETTY_PRINT) . "\n\n";
        
        // Validate response structure
        echo "=== Validation ===\n";
        echo "✓ Response is valid JSON\n";
        echo ($decoded['success'] ?? false) ? "✓ success = true\n" : "✗ success is missing or false\n";
        echo (isset($decoded['booking']['id'])) ? "✓ booking.id exists\n" : "✗ booking.id is missing\n";
        echo (isset($decoded['whatsapp_url'])) ? "✓ whatsapp_url exists\n" : "✗ whatsapp_url is missing\n";
    } else {
        echo "✗ Invalid JSON response\n";
        echo $response . "\n";
    }
} else {
    echo "✗ No response received\n";
}
