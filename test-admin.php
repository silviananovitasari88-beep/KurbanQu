<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "✓ Laravel app bootstrapped successfully\n";
echo "Routes count: " . count(\Illuminate\Support\Facades\Route::getRoutes()) . "\n";

// Test admin route exists
$routes = \Illuminate\Support\Facades\Route::getRoutes();
foreach ($routes as $route) {
    if (strpos($route->uri(), 'admin') !== false) {
        echo "✓ Found route: " . $route->uri() . "\n";
    }
}
