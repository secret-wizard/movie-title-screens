<?php
header('Content-Type: application/json');
$jsonUrl = 'https://secret-wizard.github.io/movie-title-screens/1990s/images.json';
$data = json_decode(file_get_contents($jsonUrl), true);
$images = $data['images'];
$randomIndex = array_rand($images);
$response = ['file' => $images[$randomIndex], 'timestamp' => time()];
echo json_encode($response);
?>
