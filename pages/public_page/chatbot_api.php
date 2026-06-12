<?php
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$message = trim($data["message"] ?? "");

if ($message === "") {
    echo json_encode(["reply" => "⚠️ Vui lòng nhập nội dung tin nhắn."]);
    exit;
}

$apiKey = "AQ.Ab8RN6KUpvS8dRilzfn19uFcYeKWhTwvnzXPa_BqcH6yBc7Qqg";
$model = "gemini-2.5-flash";
$endpoint = "https://generativelanguage.googleapis.com/v1beta/models/$model:generateContent?key=$apiKey";

$payload = json_encode([
    "contents" => [[
        "role" => "user",
        "parts" => [[
            "text" => "Bạn là chatbot hỗ trợ khách hàng TECHFIX, nói chuyện thân thiện, tự nhiên, và chỉ trả lời bằng tiếng Việt.\n\nNgười dùng: $message"
        ]]
    ]]
]);

$ch = curl_init($endpoint);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $payload
]);

$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpcode !== 200) {
    echo json_encode([
        "reply" => "❌ Lỗi API ($httpcode): " . $response
    ]);
    exit;
}

$result = json_decode($response, true);
$reply = $result["candidates"][0]["content"]["parts"][0]["text"] ?? "Xin lỗi, tôi chưa hiểu ý bạn.";
echo json_encode(["reply" => $reply]);
?>
