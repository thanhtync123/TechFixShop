<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0); // Tắt hiển thị lỗi cho production

// ------------------------------
// 1. Kết nối database
// ------------------------------
if (file_exists('../config/db.php')) {
    require_once '../config/db.php';
} elseif (file_exists('../../config/db.php')) {
    require_once '../../config/db.php';
} else {
    echo json_encode(['success' => false, 'message' => 'Lỗi: Không tìm thấy config/db.php']);
    exit;
}

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Lỗi kết nối CSDL: ' . $conn->connect_error]);
    exit;
}

// Lấy danh sách dịch vụ từ DB
$services_list = [];
$sql = "SELECT id, name FROM services";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $services_list[] = ['id' => intval($row['id']), 'name' => $row['name']];
    }
}
$conn->close();

// ------------------------------
// 2. Kiểm tra file upload
// ------------------------------
if (!isset($_FILES['media_file']) || $_FILES['media_file']['error'] !== UPLOAD_ERR_OK) {
    $errorMsg = 'Chưa nhận được file hoặc có lỗi upload.';
    if (isset($_FILES['media_file']['error'])) {
        $errors = [
            UPLOAD_ERR_INI_SIZE => 'File vượt quá dung lượng upload_max_filesize',
            UPLOAD_ERR_FORM_SIZE => 'File vượt quá dung lượng MAX_FILE_SIZE',
            UPLOAD_ERR_PARTIAL => 'File chỉ upload được một phần',
            UPLOAD_ERR_NO_FILE => 'Không có file nào được upload',
            UPLOAD_ERR_NO_TMP_DIR => 'Thiếu thư mục tạm',
            UPLOAD_ERR_CANT_WRITE => 'Không thể ghi file vào đĩa',
            UPLOAD_ERR_EXTENSION => 'Upload bị chặn bởi extension PHP',
        ];
        $errorMsg = $errors[$_FILES['media_file']['error']] ?? $errorMsg;
    }
    echo json_encode(['success' => false, 'message' => $errorMsg]);
    exit;
}

$fileTmp = $_FILES['media_file']['tmp_name'];
$fileType = mime_content_type($fileTmp);
$allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp']; // Vision API hỗ trợ ảnh
// Nếu muốn hỗ trợ video, cần dùng Video Intelligence API (khác)
if (!in_array($fileType, $allowedTypes)) {
    echo json_encode(['success' => false, 'message' => 'Chỉ hỗ trợ file ảnh JPEG, PNG, WEBP. Video chưa được hỗ trợ trong bản này.']);
    exit;
}

// Đọc nội dung ảnh base64
$imageData = base64_encode(file_get_contents($fileTmp));
if (!$imageData) {
    echo json_encode(['success' => false, 'message' => 'Không thể đọc file ảnh.']);
    exit;
}

// ------------------------------
// 3. Gọi Google Cloud Vision API
// ------------------------------
$apiKey = 'AIzaSyDqYaPYiQWI4YCpUrG5DkUBjQ3byewJcB8'; // THAY THẾ BẰNG API KEY CỦA BẠN
$url = 'https://vision.googleapis.com/v1/images:annotate?key=' . $apiKey;

$requestBody = [
    'requests' => [
        [
            'image' => ['content' => $imageData],
            'features' => [
                ['type' => 'LABEL_DETECTION', 'maxResults' => 10],
                ['type' => 'OBJECT_LOCALIZATION', 'maxResults' => 5],
                ['type' => 'TEXT_DETECTION'] // phát hiện chữ (có thể thấy số seri, model)
            ]
        ]
    ]
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestBody));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    $errorDetail = $response ? json_decode($response, true) : ['error' => 'Không có phản hồi'];
    echo json_encode(['success' => false, 'message' => 'Lỗi khi gọi Vision API', 'detail' => $errorDetail]);
    exit;
}

$visionData = json_decode($response, true);
$labels = $visionData['responses'][0]['labelAnnotations'] ?? [];
$objects = $visionData['responses'][0]['localizedObjectAnnotations'] ?? [];
$text = $visionData['responses'][0]['fullTextAnnotation']['text'] ?? '';

// ------------------------------
// 4. Phân tích kết quả để chọn dịch vụ phù hợp
// ------------------------------
$detectedDevice = null;
$confidence = 0;

// Tập từ khóa mapping (có thể mở rộng)
$keywordsMap = [
    'air_conditioner' => ['air conditioner', 'ac', 'điều hòa', 'máy lạnh', 'condenser', 'gas leak'],
    'computer'        => ['computer', 'laptop', 'pc', 'màn hình', 'keyboard', 'motherboard', 'máy tính', 'sọc màn hình'],
    'vehicle'         => ['car', 'xe hơi', 'motorcycle', 'xe máy', 'engine', 'động cơ', 'brake'],
    'electrical'      => ['electrical', 'điện', 'wire', 'circuit', 'aptomat', 'đoản mạch', 'ổ cắm']
];

// Duyệt qua các label (nhãn) của Vision API
foreach ($labels as $label) {
    $desc = strtolower($label['description']);
    $score = $label['score'];
    foreach ($keywordsMap as $device => $keywords) {
        foreach ($keywords as $kw) {
            if (strpos($desc, $kw) !== false && $score > $confidence) {
                $detectedDevice = $device;
                $confidence = $score;
                break 2;
            }
        }
    }
}

// Duyệt qua các object (đối tượng) nếu chưa tìm thấy
if (!$detectedDevice && !empty($objects)) {
    foreach ($objects as $obj) {
        $name = strtolower($obj['name']);
        $score = $obj['score'];
        foreach ($keywordsMap as $device => $keywords) {
            foreach ($keywords as $kw) {
                if (strpos($name, $kw) !== false && $score > $confidence) {
                    $detectedDevice = $device;
                    $confidence = $score;
                    break 2;
                }
            }
        }
    }
}

// Nếu vẫn không có, dùng text detection (tìm model, số serial)
if (!$detectedDevice && !empty($text)) {
    $textLower = strtolower($text);
    foreach ($keywordsMap as $device => $keywords) {
        foreach ($keywords as $kw) {
            if (strpos($textLower, $kw) !== false) {
                $detectedDevice = $device;
                $confidence = 0.5; // gán độ tin cậy trung bình
                break 2;
            }
        }
    }
}

// ------------------------------
// 5. Map với service có trong database
// ------------------------------
$target_service_id = null;
$target_service_name = 'Dịch vụ bảo trì chung';
$ai_description = 'Hệ thống chưa xác định rõ thiết bị. Vui lòng mô tả thêm chi tiết sự cố.';

if ($detectedDevice) {
    // Tìm service trong DB khớp với loại thiết bị
    $serviceMatch = [
        'air_conditioner' => ['lạnh', 'điều hòa', 'máy lạnh'],
        'computer'        => ['tính', 'pc', 'mạng', 'máy tính'],
        'vehicle'         => ['xe', 'ô tô', 'xe máy'],
        'electrical'      => ['điện', 'điện lạnh']
    ];
    $matched = false;
    $keywords = $serviceMatch[$detectedDevice] ?? [];
    foreach ($services_list as $srv) {
        $srvName = strtolower($srv['name']);
        foreach ($keywords as $kw) {
            if (strpos($srvName, $kw) !== false) {
                $target_service_id = $srv['id'];
                $target_service_name = $srv['name'];
                $matched = true;
                break 2;
            }
        }
    }
    if (!$matched && !empty($services_list)) {
        // fallback: dùng service đầu tiên
        $target_service_id = $services_list[0]['id'];
        $target_service_name = $services_list[0]['name'];
    }

    // Tạo mô tả AI dựa trên loại thiết bị và các label tìm được
    $topLabels = array_slice($labels, 0, 3);
    $labelStr = implode(', ', array_column($topLabels, 'description'));
    switch ($detectedDevice) {
        case 'air_conditioner':
            $ai_description = "🤖 [Google Vision AI]: Phát hiện thiết bị điện lạnh (điều hòa/máy lạnh) với các đặc điểm: $labelStr. Khuyến nghị kiểm tra rò rỉ gas, vệ sinh lưới lọc và bảo dưỡng định kỳ.";
            break;
        case 'computer':
            $ai_description = "🤖 [Google Vision AI]: Xác định thiết bị máy tính/laptop. Nhãn phát hiện: $labelStr. Có thể gặp lỗi phần cứng (màn hình, nguồn) hoặc phần mềm. Nên chạy chẩn đoán và vệ sinh bo mạch.";
            break;
        case 'vehicle':
            $ai_description = "🤖 [Google Vision AI]: Phát hiện phương tiện (xe hơi/xe máy). Dấu hiệu: $labelStr. Đề xuất kiểm tra động cơ, hệ thống truyền động và dầu nhớt.";
            break;
        case 'electrical':
            $ai_description = "🤖 [Google Vision AI]: Nhận diện thiết bị điện / mạch điện. Các đối tượng: $labelStr. Cảnh báo nguy cơ đoản mạch hoặc quá tải. Cần kiểm tra an toàn hệ thống điện.";
            break;
        default:
            $ai_description = "🤖 [Google Vision AI]: Phát hiện thiết bị dạng $detectedDevice. Mô tả chi tiết: $labelStr. Khuyến nghị điều phối kỹ thuật viên kiểm tra thực tế.";
    }
} else {
    // Không xác định được, dùng service đầu tiên trong DB nếu có
    if (!empty($services_list)) {
        $target_service_id = $services_list[0]['id'];
        $target_service_name = $services_list[0]['name'];
    }
    $ai_description = "🤖 [Google Vision AI]: Không thể xác định rõ loại thiết bị từ ảnh. Các nhãn phát hiện: " . implode(', ', array_column($labels, 'description')) . ". Vui lòng cung cấp thêm thông tin hoặc liên hệ hỗ trợ.";
}

// ------------------------------
// 6. Trả về kết quả JSON
// ------------------------------
$responseData = [
    'success' => true,
    'diagnosis' => [
        'service_id' => $target_service_id,
        'service_name' => $target_service_name,
        'description' => $ai_description,
        'raw_labels' => $labels,      // tùy chọn, có thể bỏ nếu không cần
        'confidence' => $confidence
    ]
];

echo json_encode($responseData, JSON_UNESCAPED_UNICODE);
exit;