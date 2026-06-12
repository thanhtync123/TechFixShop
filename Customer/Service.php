<?php
session_start();
require_once '../config/db.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sql = "SELECT * FROM services";

if (!empty($search)) {
    $searchSafe = $conn->real_escape_string($search);
    $sql .= " WHERE name LIKE '%$searchSafe%' OR description LIKE '%$searchSafe%'";
}

$sql .= " ORDER BY group_name DESC, id ASC";

$result = $conn->query($sql);

$serviceGroups = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $groupName = !empty($row['group_name']) ? $row['group_name'] : 'Dịch vụ khác';
        
        $imagePath = !empty($row['image']) ? $row['image'] : '/TechFixPHP/assets/image/default.jpg';

        $serviceGroups[$groupName][] = [
            'id' => $row['id'],
            'Title' => $row['name'],
            'Description' => $row['description'],
            'Image' => $imagePath
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dịch Vụ - TECHFIX</title>
    <link href="/TechFixPHP/assets/css/service.css" rel="stylesheet" />
    <style>
        .search-alert {
            text-align: center; margin: 20px auto; padding: 15px;
            background-color: #e7f1ff; border: 1px solid #b6d4fe; border-radius: 8px;
            color: #084298; max-width: 800px;
        }
        .search-alert a { color: #dc3545; font-weight: bold; text-decoration: none; margin-left: 10px; }
        .btn-home {
            position: absolute; top: 20px; left: 20px; 
            text-decoration: none; font-weight: bold; color: #333; 
            background: #f8f9fa; padding: 8px 15px; border-radius: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1); transition: 0.3s;
        }
        .btn-home:hover { background: #e2e6ea; }
    </style>
</head>
<body>

    <div class="services-page">
        <a href="/TechFixPHP/index.php" class="btn-home">⬅ Trang chủ</a>

        <header class="header">
            <h1>Dịch Vụ Của TECHFIX</h1>
            <p>Hơn 25 dịch vụ sửa chữa, lắp đặt & bảo trì chuyên nghiệp cho gia đình và doanh nghiệp.</p>
        </header>

        <?php if (!empty($search)): ?>
            <div class="search-alert">
                Kết quả tìm kiếm cho: <strong>"<?= htmlspecialchars($search) ?>"</strong>
                <a href="Service.php">(Xóa lọc)</a>
            </div>
        <?php endif; ?>

        <main class="services-wrapper">
            <?php if (empty($serviceGroups)): ?>
                <div style="text-align:center; padding: 50px; color: #666;">
                    <h3>😕 Không tìm thấy dịch vụ nào phù hợp.</h3>
                    <p>Vui lòng thử từ khóa khác.</p>
                    <a href="Service.php" class="btn">Xem tất cả dịch vụ</a>
                </div>
            <?php else: ?>
                
                <?php foreach ($serviceGroups as $groupName => $services): ?>
                    <section class="service-group">
                        <h2><?= htmlspecialchars($groupName) ?></h2>
                        <div class="services-container">
                            
                            <?php foreach ($services as $service): ?>
                                <div class="service-card">
                                    <img src="<?= htmlspecialchars($service['Image']) ?>" 
                                         alt="<?= htmlspecialchars($service['Title']) ?>"
                                         onerror="this.src='/TechFixPHP/assets/image/default.jpg'" />
                                    
                                    <div class="content">
                                        <h3><?= htmlspecialchars($service['Title']) ?></h3>
                                        <p><?= htmlspecialchars(mb_strimwidth($service['Description'], 0, 90, "...")) ?></p>
                                        <a href="/TechFixPHP/pages/public_page/service_detail.php?id=<?= $service['id'] ?>" class="btn">Xem chi tiết</a>
                                        <a href="book.php?service_id=<?= $service['id'] ?>" class="btn">Đặt Dịch Vụ</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                        </div>
                    </section>
                <?php endforeach; ?>

            <?php endif; ?>
        </main>

        <footer class="footer">
            <p>© 2025 TECHFIX - HomeTech | All Rights Reserved</p>
        </footer>
    </div>

</body>
</html>
