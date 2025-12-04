<?php
/**
 * Simple JSON test - no dependencies
 */

header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'message' => 'JSON test works!',
    'timestamp' => date('Y-m-d H:i:s'),
    'test_data' => [
        'number' => 123,
        'string' => 'hello',
        'boolean' => true
    ]
]);

