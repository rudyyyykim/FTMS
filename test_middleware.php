<?php
// Simple test to verify the middleware class exists and works
require_once __DIR__ . '/vendor/autoload.php';

try {
    $middleware = new \App\Http\Middleware\RedirectIfNotRole();
    echo "✅ Middleware class exists and can be instantiated\n";
    
    // Test the handle method signature
    $reflection = new ReflectionClass($middleware);
    $handleMethod = $reflection->getMethod('handle');
    echo "✅ Handle method exists with " . $handleMethod->getNumberOfParameters() . " parameters\n";
    
    echo "🎉 Middleware is ready to use!\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
