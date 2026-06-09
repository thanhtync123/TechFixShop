<?php
header('Content-Type: application/json');

$apiKey = 'AIzaSy...'; 

$url = "https://generativelanguage.googleapis.com/v1beta/models?key=$apiKey";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$data = json_decode($response, true);

if ($http_code !== 200) {
    echo json_encode(['ERROR' => "Key lỗi hoặc sai đường dẫn", 'detail' => $data]);
    exit;
}

$valid_models = [];
if (isset($data['models'])) {
    foreach ($data['models'] as $m) {
        if (in_array("generateContent", $m['supportedGenerationMethods'])) {
            $valid_models[] = str_replace("models/", "", $m['name']);
        }
    }
}

echo json_encode([
    'MESSAGE' => 'Hãy copy một trong các tên dưới đây và dán vào biến $model trong file code:',
    'DANH_SACH_MODEL_CUA_BAN' => $valid_models
], JSON_PRETTY_PRINT);
?>