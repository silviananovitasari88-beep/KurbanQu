<?php
/**
 * Debug script untuk test booking endpoint dengan detail
 */
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Simulate request
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;

echo "=== DEBUG BOOKING ENDPOINT ===\n\n";

// Test data
$testData = [
    'fullName' => 'Test User',
    'email' => 'testuser@email.com',
    'phone' => '081223288620',
    'roomNumber' => '105-01'
];

echo "1. Test Data:\n";
echo json_encode($testData, JSON_PRETTY_PRINT) . "\n\n";

// Create fake request
$request = Request::create('/bookings/create', 'POST', [], [], [], [], json_encode($testData));
$request->headers->set('Content-Type', 'application/json');
$request->setJson(json_decode(json_encode($testData)));

echo "2. Request Object Created\n";
echo "   Method: " . $request->getMethod() . "\n";
echo "   Path: " . $request->path() . "\n";
echo "   Is JSON: " . ($request->isJson() ? 'Yes' : 'No') . "\n\n";

// Test controller directly
echo "3. Calling Controller Method Directly:\n";
try {
    $controller = new BookingController();
    $response = $controller->storeFromWeb($request);
    
    echo "   Response Status: " . $response->getStatusCode() . "\n";
    echo "   Response Content:\n";
    
    $content = $response->getContent();
    echo $content . "\n\n";
    
    // Check if valid JSON
    $decoded = json_decode($content, true);
    if ($decoded) {
        echo "✓ Valid JSON!\n";
        echo "✓ Success: " . ($decoded['success'] ? 'TRUE' : 'FALSE') . "\n";
        if (isset($decoded['booking']['id'])) {
            echo "✓ Booking ID: " . $decoded['booking']['id'] . "\n";
        }
        if (isset($decoded['message'])) {
            echo "✓ Message: " . $decoded['message'] . "\n";
        }
    } else {
        echo "✗ INVALID JSON!\n";
        echo "JSON Error: " . json_last_error_msg() . "\n";
    }
    
} catch (\Illuminate\Validation\ValidationException $e) {
    echo "❌ VALIDATION ERROR!\n";
    echo json_encode($e->errors(), JSON_PRETTY_PRINT) . "\n";
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
