<?php
session_start();
require_once '../config/db.php'; 

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'customer') {
    header("Location: /TechFixPHP/pages/public_page/login.php");
    exit();
}

$customer_id = $_SESSION['user']['id'];
$booking_id = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : (isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0);


$query = "SELECT * FROM bookings WHERE id = ? AND customer_id = ? AND (status = 'completed' OR status = 'paid')";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $booking_id, $customer_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    die("<div style='text-align:center; padding:50px; font-family:sans-serif;'>
            <h3 style='color:red;'>⚠️ Đơn hàng không hợp lệ hoặc chưa hoàn thành!</h3>
            <a href='my_booking.php'>Quay lại</a>
         </div>");
}

$check = $conn->prepare("SELECT * FROM reviews WHERE booking_id = ? AND customer_id = ?");
$check->bind_param("ii", $booking_id, $customer_id);
$check->execute();
$review = $check->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$review) {
    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);
    $image_path = null;

    if (isset($_FILES['review_image']) && $_FILES['review_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $filename = $_FILES['review_image']['name'];
        $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($filetype, $allowed)) {
            if ($_FILES['review_image']['size'] <= 5 * 1024 * 1024) {
                $target_dir = "../../assets/uploads/reviews/";
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }

                $new_name = "review_" . $booking_id . "_" . time() . "." . $filetype;
                $target_file = $target_dir . $new_name;

                if (move_uploaded_file($_FILES['review_image']['tmp_name'], $target_file)) {
                    $image_path = $new_name; 
                }
            } else {
                echo "<script>alert('Ảnh quá lớn! Vui lòng chọn ảnh dưới 5MB.');</script>";
            }
        } else {
            echo "<script>alert('Định dạng ảnh không hỗ trợ (Chỉ JPG, PNG, GIF).');</script>";
        }
    }

    $insert = $conn->prepare("INSERT INTO reviews (booking_id, customer_id, rating, comment, image, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $insert->bind_param("iiiss", $booking_id, $customer_id, $rating, $comment, $image_path);
    
    if ($insert->execute()) {
        echo "<script>
                alert('Cảm ơn bạn đã gửi đánh giá!'); 
                window.location='my_booking.php';
              </script>";
        exit;
    } else {
        echo "<script>alert('Lỗi hệ thống: " . $conn->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Đánh giá dịch vụ #<?= $booking_id ?> - TECHFIX</title>
<link rel="stylesheet" href="../../assets/css/customer.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f2f5; margin: 0; padding: 40px 20px; }
    .container {
        max-width: 600px; margin: auto; background: white; padding: 40px;
        border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    }
    h2 { text-align: center; margin-bottom: 30px; color: #333; font-weight: 700; }
    
    .stars { display: flex; flex-direction: row-reverse; justify-content: center; gap: 10px; margin-bottom: 20px; }
    .stars input { display: none; }
    .stars label { font-size: 35px; color: #ddd; cursor: pointer; transition: color 0.2s; }
    .stars input:checked ~ label, .stars label:hover, .stars label:hover ~ label { color: #ffc107; text-shadow: 0 0 5px #ffc107; }

    .form-group { margin-bottom: 20px; }
    label.control-label { font-weight: 600; color: #555; margin-bottom: 8px; display: block; }
    
    textarea {
        width: 100%; height: 120px; border: 2px solid #eee; border-radius: 12px;
        padding: 15px; font-family: inherit; font-size: 14px; resize: none; outline: none; transition: 0.3s; box-sizing: border-box;
    }
    textarea:focus { border-color: #0099ff; }

    .upload-box {
        border: 2px dashed #ccc; border-radius: 12px; padding: 20px; text-align: center;
        background: #fafafa; cursor: pointer; position: relative; transition: 0.3s;
    }
    .upload-box:hover { background: #f0f8ff; border-color: #0099ff; }
    .upload-box input[type="file"] {
        position: absolute; width: 100%; height: 100%; top: 0; left: 0; opacity: 0; cursor: pointer;
    }
    .upload-icon { font-size: 24px; color: #0099ff; margin-bottom: 10px; }
    .upload-text { font-size: 13px; color: #666; }
    #preview-img { max-width: 100%; max-height: 200px; margin-top: 15px; border-radius: 8px; display: none; }

    button.btn-submit {
        background: #0099ff; color: white; border: none; padding: 15px 20px;
        border-radius: 30px; margin-top: 10px; cursor: pointer; width: 100%;
        font-size: 16px; font-weight: bold; box-shadow: 0 4px 15px rgba(0,153,255,0.3);
        transition: 0.3s;
    }
    button.btn-submit:hover { background: #007acc; transform: translateY(-2px); }

    .reviewed-box { text-align: center; padding: 20px; }
    .review-img-display { max-width: 100%; border-radius: 10px; margin-top: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
    .back-link { display: inline-block; margin-top: 20px; color: #0099ff; text-decoration: none; font-weight: 600; }
</style>
</head>
<body>

<div class="container">
    <?php if ($review): ?>
        <div class="reviewed-box">
            <i class="fa-solid fa-circle-check" style="font-size: 50px; color: #28a745; margin-bottom: 15px;"></i>
            <h3>Bạn đã đánh giá đơn này</h3>
            <p style="font-size: 24px; margin: 10px 0;">
                <?= str_repeat('⭐', $review['rating']) ?><span style="filter: grayscale(100%); opacity: 0.3;"><?= str_repeat('⭐', 5 - $review['rating']) ?></span>
            </p>
            <p style="color: #555; font-style: italic;">"<?= htmlspecialchars($review['comment']) ?>"</p>

            <?php if (!empty($review['image'])): ?>
                <div>
                    <img src="../../assets/uploads/reviews/<?= $review['image'] ?>" class="review-img-display" alt="Ảnh đánh giá">
                </div>
            <?php endif; ?>

            <a href="my_booking.php" class="back-link"><i class="fa-solid fa-arrow-left"></i> Quay lại lịch sử</a>
        </div>

    <?php else: ?>
        <h2><i class="fa-solid fa-star" style="color: #ffc107;"></i> Đánh giá dịch vụ</h2>
        <p style="text-align: center; color: #666; margin-bottom: 25px;">
            Hãy chia sẻ trải nghiệm của bạn để chúng tôi phục vụ tốt hơn!
        </p>

        <form method="post" enctype="multipart/form-data">
            
            <div class="stars">
                <input type="radio" name="rating" value="5" id="star5" required><label for="star5" title="Tuyệt vời">★</label>
                <input type="radio" name="rating" value="4" id="star4"><label for="star4" title="Tốt">★</label>
                <input type="radio" name="rating" value="3" id="star3"><label for="star3" title="Bình thường">★</label>
                <input type="radio" name="rating" value="2" id="star2"><label for="star2" title="Tệ">★</label>
                <input type="radio" name="rating" value="1" id="star1"><label for="star1" title="Rất tệ">★</label>
            </div>

            <div class="form-group">
                <label class="control-label">Nhận xét của bạn:</label>
                <textarea name="comment" placeholder="Kỹ thuật viên có thân thiện không? Dịch vụ thế nào?..."></textarea>
            </div>

            <div class="form-group">
                <label class="control-label">Hình ảnh thực tế (Tùy chọn):</label>
                <div class="upload-box" onclick="document.getElementById('fileInput').click()">
                    <input type="file" name="review_image" id="fileInput" accept="image/*" onchange="previewImage(event)">
                    <div class="upload-icon"><i class="fa-solid fa-cloud-arrow-up"></i></div>
                    <div class="upload-text">Nhấn để chọn ảnh hoặc kéo thả vào đây<br><span style="font-size: 11px; color: #999;">(JPG, PNG - Max 5MB)</span></div>
                    <img id="preview-img">
                </div>
            </div>

            <button type="submit" class="btn-submit">GỬI ĐÁNH GIÁ</button>
        </form>
    <?php endif; ?>
</div>

<script>
    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function(){
            var output = document.getElementById('preview-img');
            output.src = reader.result;
            output.style.display = 'block';
            
            document.querySelector('.upload-icon').style.display = 'none';
            document.querySelector('.upload-text').style.display = 'none';
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>

</body>
</html>