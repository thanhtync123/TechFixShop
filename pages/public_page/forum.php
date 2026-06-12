<?php
session_start();
require_once '../../config/db.php';

// Xử lý Đăng câu hỏi mới
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn_ask'])) {
    if (!isset($_SESSION['user'])) {
        echo "<script>alert('Vui lòng đăng nhập để hỏi!');</script>";
    } else {
        $title = $conn->real_escape_string($_POST['title']);
        $content = $conn->real_escape_string($_POST['content']);
        $uid = $_SESSION['user']['id'] ?? 0; // Giả sử lưu session dạng mảng
        $uname = $_SESSION['user']['name'] ?? 'Khách'; // Lấy tên từ session

        $conn->query("INSERT INTO forum_questions (user_id, user_name, title, content) VALUES ($uid, '$uname', '$title', '$content')");
        header("Location: forum.php"); // Load lại trang
        exit;
    }
}

// Lấy danh sách câu hỏi
$sql = "SELECT * FROM forum_questions ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Cộng Đồng TechFix - Hỏi Đáp</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f8f9fa; }
        .forum-header { background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%); color: white; padding: 40px 0; margin-bottom: 30px; }
        .question-card { border: none; border-bottom: 1px solid #eee; transition: 0.2s; }
        .question-card:hover { background: #f1f3f5; transform: translateX(5px); }
        .avatar-circle { width: 40px; height: 40px; background: #ddd; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #555; }
        .stat-box { font-size: 0.8rem; color: #888; text-align: center; min-width: 60px; }
        .stat-num { font-size: 1.1rem; font-weight: bold; color: #333; display: block; }
    </style>
</head>
<body>

    <div class="forum-header text-center">
        <div class="container">
            <h1 class="fw-bold"><i class="fa-solid fa-users-line"></i> CỘNG ĐỒNG TECHFIX</h1>
            <p class="lead">Nơi chia sẻ kinh nghiệm, hỏi đáp về sửa chữa điện lạnh</p>
            <button class="btn btn-warning fw-bold px-4 rounded-pill mt-2" data-bs-toggle="modal" data-bs-target="#askModal">
                <i class="fa-solid fa-pen-to-square"></i> Đặt Câu Hỏi Mới
            </button>
        </div>
    </div>

    <div class="container mb-5">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-header bg-white py-3 fw-bold border-bottom">
                        <i class="fa-solid fa-fire text-danger"></i> Thảo luận mới nhất
                    </div>
                    <div class="card-body p-0">
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): 
                                // Random màu avatar cho đẹp
                                $colors = ['#ffadad', '#ffd6a5', '#fdffb6', '#caffbf', '#9bf6ff', '#a0c4ff', '#bdb2ff'];
                                $bg_color = $colors[array_rand($colors)];
                                $first_char = mb_substr($row['user_name'], 0, 1);
                                
                                // Đếm số trả lời
                                $qid = $row['id'];
                                $ans_count = $conn->query("SELECT COUNT(*) as c FROM forum_answers WHERE question_id = $qid")->fetch_assoc()['c'];
                            ?>
                            <div class="p-3 question-card d-flex align-items-center">
                                <div class="avatar-circle me-3" style="background: <?= $bg_color ?>">
                                    <?= $first_char ?>
                                </div>
                                
                                <div class="flex-grow-1">
                                    <h5 class="mb-1">
                                        <a href="forum_detail.php?id=<?= $row['id'] ?>" class="text-decoration-none text-dark fw-bold">
                                            <?= htmlspecialchars($row['title']) ?>
                                        </a>
                                    </h5>
                                    <small class="text-muted">
                                        Đăng bởi <strong><?= $row['user_name'] ?></strong> • <?= date('d/m H:i', strtotime($row['created_at'])) ?>
                                    </small>
                                </div>

                                <div class="d-flex">
                                    <div class="stat-box">
                                        <span class="stat-num"><?= $row['views'] ?></span> Views
                                    </div>
                                    <div class="stat-box">
                                        <span class="stat-num text-primary"><?= $ans_count ?></span> Trả lời
                                    </div>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="text-center p-5 text-muted">Chưa có câu hỏi nào. Hãy là người đầu tiên!</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="askModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">📝 Đặt câu hỏi mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tiêu đề ngắn gọn</label>
                        <input type="text" name="title" class="form-control" placeholder="Ví dụ: Máy lạnh không mát..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Chi tiết vấn đề</label>
                        <textarea name="content" class="form-control" rows="5" placeholder="Mô tả kỹ hơn về tình trạng..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" name="btn_ask" class="btn btn-primary">Đăng Ngay</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>