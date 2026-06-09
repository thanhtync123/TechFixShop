<?php
session_start();
require_once '../../config/db.php';

if (!isset($_GET['id'])) die("Không tìm thấy bài viết.");
$qid = intval($_GET['id']);

// Tăng view
$conn->query("UPDATE forum_questions SET views = views + 1 WHERE id = $qid");

// Xử lý gửi trả lời
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn_reply'])) {
    if (!isset($_SESSION['user'])) {
        echo "<script>alert('Vui lòng đăng nhập để bình luận!');</script>";
    } else {
        $content = $conn->real_escape_string($_POST['reply_content']);
        $uid = $_SESSION['user']['id'] ?? 0;
        $uname = $_SESSION['user']['name'] ?? 'Khách';
        
        $conn->query("INSERT INTO forum_answers (question_id, user_id, user_name, content) VALUES ($qid, $uid, '$uname', '$content')");
        header("Location: forum_detail.php?id=$qid");
        exit;
    }
}

// Lấy thông tin câu hỏi
$question = $conn->query("SELECT * FROM forum_questions WHERE id = $qid")->fetch_assoc();
if (!$question) die("Bài viết không tồn tại.");

// Lấy danh sách trả lời
$answers = $conn->query("SELECT * FROM forum_answers WHERE question_id = $qid ORDER BY created_at ASC");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($question['title']) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f0f2f5; }
        .qa-container { max-width: 800px; margin: 30px auto; }
        .main-post { background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .post-header { background: #e7f1ff; padding: 20px; border-bottom: 1px solid #cce5ff; }
        .post-content { padding: 30px; font-size: 1.1rem; line-height: 1.6; color: #333; }
        
        .answer-box { background: white; border-radius: 10px; padding: 20px; margin-top: 20px; border-left: 4px solid #ddd; }
        .answer-box.admin-reply { border-left-color: #0d6efd; background: #f8fbff; }
        
        .user-meta { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; }
        .avatar-sm { width: 35px; height: 35px; background: #ccc; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; }
    </style>
</head>
<body>
    
    <nav class="navbar navbar-light bg-white shadow-sm mb-4">
        <div class="container">
            <a href="forum.php" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left"></i> Quay lại Diễn đàn</a>
            <span class="navbar-brand mb-0 h1 mx-auto text-primary fw-bold">TECHFIX FORUM</span>
        </div>
    </nav>

    <div class="container qa-container">
        
        <div class="main-post">
            <div class="post-header">
                <h3 class="fw-bold mb-3"><?= htmlspecialchars($question['title']) ?></h3>
                <div class="user-meta text-muted small">
                    <i class="fa-solid fa-user-circle"></i> <?= $question['user_name'] ?> 
                    <span class="mx-2">•</span> 
                    <i class="fa-regular fa-clock"></i> <?= date('d/m/Y H:i', strtotime($question['created_at'])) ?>
                    <span class="mx-2">•</span>
                    <i class="fa-solid fa-eye"></i> <?= $question['views'] ?> lượt xem
                </div>
            </div>
            <div class="post-content">
                <?= nl2br(htmlspecialchars($question['content'])) ?>
            </div>
        </div>

        <h5 class="mt-5 mb-3 fw-bold text-secondary"><i class="fa-solid fa-comments"></i> Bình Luận (<?= $answers->num_rows ?>)</h5>
        
        <?php if ($answers->num_rows > 0): ?>
            <?php while ($ans = $answers->fetch_assoc()): 
                // Style đặc biệt nếu là Admin trả lời (Giả sử user_id=1 là admin)
                $isAdmin = ($ans['user_id'] == 1) ? 'admin-reply' : '';
                $badge = ($ans['user_id'] == 1) ? '<span class="badge bg-primary ms-2">Admin/Kỹ thuật</span>' : '';
            ?>
            <div class="answer-box shadow-sm <?= $isAdmin ?>">
                <div class="user-meta">
                    <div class="avatar-sm bg-secondary"><?= mb_substr($ans['user_name'], 0, 1) ?></div>
                    <div>
                        <strong class="text-dark"><?= $ans['user_name'] ?></strong> <?= $badge ?>
                        <div class="text-muted small" style="font-size: 0.8rem;"><?= date('d/m/Y H:i', strtotime($ans['created_at'])) ?></div>
                    </div>
                </div>
                <div class="mt-2 text-dark">
                    <?= nl2br(htmlspecialchars($ans['content'])) ?>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-muted fst-italic">Chưa có câu trả lời nào. Hãy là người đầu tiên giúp đỡ bạn ấy!</p>
        <?php endif; ?>

        <div class="card mt-5 mb-5 shadow-sm border-0">
            <div class="card-body bg-light">
                <h6 class="fw-bold"><i class="fa-solid fa-reply"></i> Gửi câu trả lời của bạn</h6>
                <form method="POST">
                    <textarea name="reply_content" class="form-control mb-3" rows="3" placeholder="Nhập nội dung thảo luận..." required></textarea>
                    <div class="text-end">
                        <button type="submit" name="btn_reply" class="btn btn-primary px-4">
                            <i class="fa-solid fa-paper-plane"></i> Gửi Bình Luận
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

</body>
</html>