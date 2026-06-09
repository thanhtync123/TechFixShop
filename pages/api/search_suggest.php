<?php
require_once '../../config/db.php'; 

header('Content-Type: application/json; charset=utf-8');

if (isset($_GET['keyword'])) {
    $keyword = "%" . trim($_GET['keyword']) . "%";
    
    
    $sql = "SELECT id, name, price, image FROM services WHERE name LIKE ? LIMIT 5";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $keyword);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $services = [];
    while ($row = $result->fetch_assoc()) {
        $services[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'price' => number_format($row['price'], 0, ',', '.') . ' đ',
            'image' => $row['image'] 
        ];
    }
    
    echo json_encode($services);
}
?>