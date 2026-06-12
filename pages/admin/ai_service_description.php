<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Bạn không có quyền sử dụng tính năng này.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$serviceName = trim($data['name'] ?? '');
$unit = trim($data['unit'] ?? '');

if ($serviceName === '') {
    http_response_code(422);
    echo json_encode([
        'success' => false,
        'message' => 'Vui lòng nhập tên dịch vụ trước khi dùng AI.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

function fallbackDescription(string $serviceName, string $unit): string
{
    $unitText = $unit !== '' ? " theo đơn vị $unit" : '';

    return "Lắp đặt, kiểm tra hoặc sửa chữa $serviceName$unitText; xử lý các lỗi thường gặp, tư vấn phương án khắc phục phù hợp và đảm bảo thiết bị/hệ thống hoạt động ổn định sau khi hoàn tất.";
}

$fallback = fallbackDescription($serviceName, $unit);
$description = '';
$source = 'fallback';

$apiKey = getenv('GEMINI_API_KEY') ?: 'AQ.Ab8RN6KUpvS8dRilzfn19uFcYeKWhTwvnzXPa_BqcH6yBc7Qqg';
$model = 'gemini-2.5-flash';
$endpoint = "https://generativelanguage.googleapis.com/v1beta/models/$model:generateContent?key=$apiKey";

$prompt = "Bạn là trợ lý nội dung cho hệ thống dịch vụ sửa chữa TECHFIX.\n"
    . "Hãy viết 1 đoạn mô tả dịch vụ bằng tiếng Việt, tự nhiên, chuyên nghiệp, dài tối đa 1-2 câu.\n"
    . "Không dùng markdown, không đánh số bước, không thêm tiêu đề, không nhắc đến AI.\n"
    . "Tên dịch vụ: $serviceName\n"
    . ($unit !== '' ? "Đơn vị tính: $unit\n" : '')
    . "Ví dụ phong cách: Lắp đặt mới hoặc sửa chữa máy bơm không lên nước, kêu to.";

$payload = json_encode([
    'contents' => [[
        'role' => 'user',
        'parts' => [['text' => $prompt]]
    ]],
    'generationConfig' => [
        'temperature' => 0.35,
        'maxOutputTokens' => 120
    ]
], JSON_UNESCAPED_UNICODE);

if (function_exists('curl_init')) {
    $ch = curl_init($endpoint);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_TIMEOUT => 15
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response && $httpCode === 200) {
        $result = json_decode($response, true);
        $description = trim($result['candidates'][0]['content']['parts'][0]['text'] ?? '');
        $description = preg_replace('/\s+/', ' ', $description);
        $source = $description !== '' ? 'gemini' : 'fallback';
    }
}

if ($description === '') {
    $description = $fallback;
}

echo json_encode([
    'success' => true,
    'description' => $description,
    'source' => $source
], JSON_UNESCAPED_UNICODE);
