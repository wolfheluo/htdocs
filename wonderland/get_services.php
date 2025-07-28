<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Read and return services.json
$services_file = __DIR__ . '/services.json';

if (file_exists($services_file)) {
    $services_data = file_get_contents($services_file);
    
    // Validate JSON
    $json_data = json_decode($services_data, true);
    
    if (json_last_error() === JSON_ERROR_NONE) {
        // Return the JSON data
        echo $services_data;
    } else {
        // JSON parsing error
        http_response_code(500);
        echo json_encode([
            'error' => 'Invalid JSON format',
            'services' => []
        ]);
    }
} else {
    // File not found
    http_response_code(404);
    echo json_encode([
        'error' => 'Services file not found',
        'services' => []
    ]);
}
?>
