<?php
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => 'http://localhost/aulia_kost/public/admin/login',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_TIMEOUT => 10,
]);

echo "Testing: http://localhost/aulia_kost/public/admin/login\n\n";

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

curl_close($ch);

echo "Status: $httpCode\n";

if ($error) {
    echo "Error: $error\n";
} else {
    // Check if response contains HTML or error
    if (strpos($response, 'error') !== false || strpos($response, 'Error') !== false || strpos($response, 'ERROR') !== false) {
        echo "⚠️ Response contains error:\n";
        echo substr($response, 0, 500) . "\n";
    } elseif (strpos($response, 'Login') !== false || strpos($response, 'admin') !== false) {
        echo "✓ Login page loaded successfully!\n";
    } else {
        echo "Response preview:\n";
        echo substr($response, 0, 300) . "\n";
    }
}
