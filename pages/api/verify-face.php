<?php
session_start();
include "../../config/db.php"; 


function calculateEuclideanDistance($desc1, $desc2) {
    $sum = 0;
    if (count($desc1) !== count($desc2)) {
        return 999; 
    }
    for ($i = 0; $i < count($desc1); $i++) {
        $diff = ($desc1[$i] ?? 0) - ($desc2[$i] ?? 0);
        $sum += $diff * $diff;
    }
    return sqrt($sum);
}


$data = json_decode(file_get_contents('php://input'), true);
$newDescriptor = $data['descriptor'] ?? null;

if (!$newDescriptor || count($newDescriptor) !== 128) {
    http_response_code(400);
    echo json_encode(['error' => 'Không có dữ liệu khuôn mặt.']);
    exit;
}

$query = "SELECT id, name, phone, role, password, address, face_descriptor FROM users WHERE face_descriptor IS NOT NULL";
$result = $conn->query($query);

if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['error' => 'Không có khuôn mặt nào trong CSDL.']);
    exit;
}

$all_users_with_faces = $result->fetch_all(MYSQLI_ASSOC);

$matchThreshold = 0.6; 
$bestMatchUser = null;
$bestDistance = 1; 

foreach ($all_users_with_faces as $user) {
    $storedDescriptor = json_decode($user['face_descriptor'], true);

    if (empty($storedDescriptor) || count($storedDescriptor) !== 128) {
        continue; 
    }

    $distance = calculateEuclideanDistance($newDescriptor, $storedDescriptor);

    if ($distance < $bestDistance && $distance < $matchThreshold) {
        $bestDistance = $distance;
        $bestMatchUser = $user;
    }
}

if ($bestMatchUser) {
    
    $_SESSION['user'] = $bestMatchUser;
    $_SESSION['phone'] = $bestMatchUser['phone'];
    $_SESSION['name'] = $bestMatchUser['name'];
    $_SESSION['role'] = $bestMatchUser['role'];
    $_SESSION['user_id'] = $bestMatchUser['id']; 

  
    echo json_encode([
        'id' => $bestMatchUser['id'],
        'name' => $bestMatchUser['name'],
        'phone' => $bestMatchUser['phone'],
        'role' => $bestMatchUser['role']
    ]);
    
} else {
    http_response_code(401);
    echo json_encode(['error' => 'Không nhận dạng được khuôn mặt.']);
}

$conn->close();
?>