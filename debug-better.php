<?php
/**
 * Better debug script dengan proper Laravel testing
 */
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Testing\TestResponse;
use Illuminate\Http\Request;

echo "=== TESTING ENDPOINT WITH PROPER REQUEST ===\n\n";

$testData = [
    'fullName' => 'Andi Wijaya',
    'email' => 'andi.wijaya@email.com',
    'phone' => '081234567890',
    'roomNumber' => '105-01'
];

echo "Test Data:\n";
echo json_encode($testData, JSON_PRETTY_PRINT) . "\n\n";

// Check route exists
echo "Checking Routes:\n";
$routes = \Illuminate\Support\Facades\Route::getRoutes();
$found = false;
foreach ($routes as $route) {
    if (strpos($route->uri(), 'bookings/create') !== false) {
        echo "✓ Route found: " . $route->uri() . "\n";
        echo "  Methods: " . implode(', ', $route->methods()) . "\n";
        $found = true;
    }
}

if (!$found) {
    echo "✗ Route not found!\n";
}

echo "\n";

// Check database tables
echo "Checking Database Tables:\n";
try {
    $kosCount = \App\Models\Kos::count();
    echo "✓ Kos table: $kosCount records\n";
    
    $userCount = \App\Models\User::count();
    echo "✓ User table: $userCount records\n";
    
    $bookingCount = \App\Models\Booking::count();
    echo "✓ Booking table: $bookingCount records\n";
} catch (\Exception $e) {
    echo "✗ Database error: " . $e->getMessage() . "\n";
}

echo "\n";

// Check controller method signature
echo "Checking Controller Method:\n";
$controllerClass = new ReflectionClass(\App\Http\Controllers\BookingController::class);
if ($controllerClass->hasMethod('storeFromWeb')) {
    $method = $controllerClass->getMethod('storeFromWeb');
    echo "✓ Method exists: storeFromWeb\n";
    echo "  Params: " . implode(', ', array_map(fn($p) => $p->getName(), $method->getParameters())) . "\n";
} else {
    echo "✗ Method not found\n";
}

echo "\n";

// Test actual form submission
echo "Testing Form Submission:\n";
try {
    // Create proper FormRequest
    $request = Request::create('/bookings/create', 'POST', $testData);
    $request->headers->set('Content-Type', 'application/x-www-form-urlencoded');
    
    $controller = new \App\Http\Controllers\BookingController();
    $response = $controller->storeFromWeb($request);
    
    echo "✓ Request processed\n";
    echo "  Status Code: " . $response->getStatusCode() . "\n";
    
    $content = $response->getContent();
    $decoded = json_decode($content, true);
    
    if (json_last_error() === JSON_ERROR_NONE && $decoded) {
        echo "✓ Response is valid JSON\n";
        echo "  Success: " . ($decoded['success'] ? 'YES' : 'NO') . "\n";
        if (isset($decoded['booking']['id'])) {
            echo "  Booking ID: " . $decoded['booking']['id'] . "\n";
        }
        if (isset($decoded['message'])) {
            echo "  Message: " . $decoded['message'] . "\n";
        }
    } else {
        echo "✗ Response is NOT valid JSON!\n";
        echo "  JSON Error: " . json_last_error_msg() . "\n";
        echo "  Response preview: " . substr($content, 0, 200) . "\n";
    }
    
} catch (\Illuminate\Validation\ValidationException $e) {
    echo "✗ Validation Error:\n";
    echo json_encode($e->errors(), JSON_PRETTY_PRINT) . "\n";
} catch (\Throwable $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
    echo "  File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
