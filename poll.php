<?php
// Simplified headers for TRMNL compatibility
header('Access-Control-Allow-Origin: *');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Check if we should return URL or serve image
$mode = isset($_GET['mode']) ? $_GET['mode'] : 'url';
$debug = isset($_GET['debug']) && $_GET['debug'] == '1';

$jsonUrl = 'https://secret-wizard.github.io/movie-title-screens/1990s/images.json';

try {
    // Fetch JSON with error handling
    $jsonContent = @file_get_contents($jsonUrl);
    if ($jsonContent === false) {
        throw new Exception('Unable to fetch data from source');
    }

    // Decode JSON with error handling
    $data = json_decode($jsonContent, true);
    if ($data === null || !isset($data['images'])) {
        throw new Exception('Invalid JSON structure');
    }

    $images = $data['images'];

    // Check if images array is not empty
    if (empty($images)) {
        throw new Exception('No images found');
    }

    // Get random image URL and validate it
    $randomImageUrl = $images[array_rand($images)];
    
    // Ensure the URL is complete and valid
    if (!filter_var($randomImageUrl, FILTER_VALIDATE_URL)) {
        throw new Exception('Invalid image URL generated');
    }

    if ($mode === 'image') {
        // Serve the actual image data
        $imageData = @file_get_contents($randomImageUrl);
        if ($imageData === false) {
            throw new Exception('Unable to fetch image data');
        }

        // Get image info to set proper content type
        $imageInfo = @getimagesizefromstring($imageData);
        if ($imageInfo !== false) {
            header('Content-Type: ' . $imageInfo['mime']);
        } else {
            // Default to JPEG if we can't determine type
            header('Content-Type: image/jpeg');
        }
        
        header('Content-Length: ' . strlen($imageData));
        header('Cache-Control: public, max-age=3600'); // Cache for 1 hour
        
        echo $imageData;
        
    } else {
        // Return URL mode (for testing)
        header('Content-Type: text/plain; charset=utf-8');
        
        if ($debug) {
            header('Content-Type: application/json');
            echo json_encode([
                'url' => trim($randomImageUrl),
                'proxy_url' => 'Add ?mode=image to get the actual image',
                'total_images' => count($images),
                'debug' => 'URL validation passed'
            ]);
        } else {
            echo trim($randomImageUrl);
        }
    }

} catch (Exception $e) {
    http_response_code(500);
    header('Content-Type: text/plain');
    echo 'Error: ' . $e->getMessage();
    exit;
}
?>
