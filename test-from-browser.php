<?php
/**
 * Test dengan simulasi request dari browser/public folder
 */

// Simulate access dari public folder
echo "=== TESTING FROM PUBLIC FOLDER CONTEXT ===\n\n";

// Current location: http://localhost/aulia_kost/
// JavaScript fetch path: ./bookings/create
// Resolved to: http://localhost/aulia_kost/bookings/create ✓ CORRECT

// Test dengan curl dari public folder perspective
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => 'http://localhost/aulia_kost/bookings/create',  // PATH YANG BENAR
    CURLOPT_POST => 1,
    CURLOPT_POSTFIELDS => json_encode([
        'fullName' => 'Budi Test',
        'email' => 'budi.test@email.com',
        'phone' => '081234567890',
        'roomNumber' => '105-01'
    ]),
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Accept: application/json',
    ],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 10,
]);

echo "Request URL: http://localhost/aulia_kost/bookings/create\n";
echo "Method: POST\n";
echo "Content-Type: application/json\n\n";

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

curl_close($ch);

echo "HTTP Status: $httpCode\n";
echo "Response:\n";
echo $response . "\n\n";

// Parse response
$decoded = json_decode($response, true);
if (json_last_error() === JSON_ERROR_NONE && $decoded) {
    echo "✓ Valid JSON!\n";
    echo "✓ Success: " . ($decoded['success'] ? 'TRUE' : 'FALSE') . "\n";
    echo "✓ Booking ID: " . $decoded['booking']['id'] . "\n";
} else {
    echo "✗ Invalid JSON!\n";
    echo "Error: " . json_last_error_msg() . "\n";
}
