<?php
header('Content-Type: application/json');

if (file_exists('../config/db.php')) {
    require_once '../config/db.php';
} elseif (file_exists('../../config/db.php')) {
    require_once '../../config/db.php';
} else {
    echo json_encode(['success'=>false, 'message'=>'Lỗi: Không tìm thấy file config/db.php']); 
    exit;
}

if ($conn->connect_error) { 
    echo json_encode(['success'=>false, 'message'=>'Lỗi kết nối CSDL']); 
    exit; 
}

$sql = "SELECT id, name FROM services";
$result = $conn->query($sql);
$prompt_list = "";
$valid_services = []; 

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $valid_services[$row['id']] = $row['name'];
        $prompt_list .= "- ID {$row['id']}: {$row['name']}\n";
    }
}

if (!isset($_FILES['media_file'])) { 
    echo json_encode(['success'=>false, 'message'=>'Chưa nhận được file ảnh/video']); 
    exit; 
}

$apiKey = "AIzaSyAcRoUIw3xXmFTcQKxUnZTzoxaAgxGOeDk"; 
$model = "gemini-2.5-flash"; 
$endpoint = "https://generativelanguage.googleapis.com/v1beta/models/$model:generateContent?key=$apiKey";

$base64_image = base64_encode(file_get_contents($_FILES['media_file']['tmp_name']));
$mime_type = $_FILES['media_file']['type'];

$prompt = "
Bạn là kỹ thuật viên trưởng tại TECHFIX.
Dưới đây là danh sách các dịch vụ sửa chữa:
$prompt_list

Yêu cầu:
1. Phân tích hình ảnh lỗi của khách hàng.
2. Chọn CHÍNH XÁC 1 ID dịch vụ phù hợp nhất từ danh sách trên.
3. Trả về JSON thuần tuý, không Markdown.

Mẫu JSON bắt buộc:
{
  \"service_id\": 1,
  \"reason\": \"Mô tả ngắn gọn lỗi và lý do chọn dịch vụ này\"
}
";

$payload = json_encode([
    "contents" => [[
        "parts" => [
            ["text" => $prompt],
            ["inline_data" => ["mime_type" => $mime_type, "data" => $base64_image]]
        ]
    ]],
    "generationConfig" => [
        "response_mime_type" => "application/json" 
    ]
]);

$ch = curl_init($endpoint);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code !== 200) {
    echo json_encode(['success'=>false, 'message'=>"Lỗi API Google ($http_code)", 'debug'=>json_decode($response)]); 
    exit;
}

$data = json_decode($response, true);
$raw_text = $data["candidates"][0]["content"]["parts"][0]["text"] ?? "{}";

$clean_json = preg_replace('/```json|```/', '', $raw_text);
$ai_result = json_decode($clean_json, true);

if ($ai_result && isset($ai_result['service_id'])) {
    $s_id = intval($ai_result['service_id']);
    
    if (isset($valid_services[$s_id])) {
        echo json_encode([
            'success' => true,
            'diagnosis' => [
                'service_id' => $s_id,
                'service_name' => $valid_services[$s_id],
                'description' => $ai_result['reason']
            ]
        ]);
    } else {
        echo json_encode(['success'=>false, 'message'=>"AI chọn ID $s_id nhưng dịch vụ này không có trong DB."]);
    }
} else {
    echo json_encode(['success'=>false, 'message'=>'AI không trả về kết quả hợp lệ.', 'raw'=>$raw_text]);
}
?>