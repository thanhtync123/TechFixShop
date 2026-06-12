<?php
// api_get_districts.php
header('Content-Type: application/json');
require_once '../config/db.php'; 

$province_code = $_GET['province_id'] ?? ''; 

$districts = [];

if (!empty($province_code)) { 
    try {
       
        $sql = "SELECT name FROM districts WHERE province_code = ? ORDER BY name ASC";
        
        $stmt = $conn->prepare($sql);
        
        $stmt->bind_param("s", $province_code);
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $districts[] = $row; 
        }
        $stmt->close();
        
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    }
}

echo json_encode($districts);
?>