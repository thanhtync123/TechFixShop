<?php
session_start();
if (!isset($_SESSION['user']) || ($_SESSION['role'] !== 'technical' && $_SESSION['role'] !== 'admin')) {
    die("Bạn không có quyền truy cập.");
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechFix Scanner</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <style>
        :root { --primary: #0d6efd; --bg: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); }

        body {
            background: var(--bg); color: white;
            min-height: 100vh; display: flex; flex-direction: column; align-items: center;
            font-family: 'Segoe UI', sans-serif; padding: 20px 10px;
        }

        .scanner-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 24px;
            padding: 25px 20px;
            width: 100%; max-width: 480px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        #reader {
            width: 100%;
            margin-top: 15px;
            border-radius: 15px;
            overflow: hidden;
            background: rgba(0,0,0,0.3); 
            border: none !important;
        }

        #html5-qrcode-button-camera-permission {
            display: inline-block;
            background-color: #0d6efd !important;
            color: white !important;
            padding: 12px 24px !important;
            border-radius: 50px !important;
            border: none !important;
            font-weight: 600 !important;
            cursor: pointer !important;
            margin: 20px 0 !important;
            font-size: 16px !important;
            box-shadow: 0 4px 15px rgba(13, 110, 253, 0.4);
        }

        #reader__dashboard_section_csr span, #reader__dashboard_section_swaplink { display: none !important; }

        .result-box {
            display: none; background: white; color: #333;
            padding: 20px; border-radius: 15px; margin-top: 20px;
            animation: slideUp 0.3s ease-out;
        }
        @keyframes slideUp { from {transform: translateY(20px); opacity: 0;} to {transform: translateY(0); opacity: 1;} }

        .btn-home {
            margin-top: 30px;
            color: rgba(255,255,255,0.8); text-decoration: none;
            display: flex; align-items: center; gap: 8px;
            transition: 0.3s;
        }
        .btn-home:hover { color: white; transform: translateX(-5px); }
    </style>
</head>
<body>

    <div class="scanner-card">
        <h3 class="fw-bold mb-1"><i class="fa-solid fa-qrcode"></i> TechFix Scanner</h3>
        <p class="small text-white-50 mb-0">Di chuyển camera vào mã QR</p>

        <div id="reader"></div>

        <div id="resultBox" class="result-box">
            <div class="mb-2"><i id="statusIcon" class="fa-solid fa-spinner fa-spin fa-3x text-primary"></i></div>
            <h4 id="statusTitle" class="fw-bold">Đang xử lý...</h4>
            <p id="statusMsg" class="text-secondary mb-3">...</p>
            <button class="btn btn-primary w-100 rounded-pill" onclick="location.reload()">Quét tiếp</button>
        </div>
    </div>

    <a href="tech_schedule.php" class="btn-home"><i class="fa-solid fa-arrow-left"></i> Quay lại lịch làm việc</a>

    <script>
        const resultBox = document.getElementById('resultBox');
        const readerDiv = document.getElementById('reader');

        function onScanSuccess(decodedText, decodedResult) {
            html5QrcodeScanner.clear();
            readerDiv.style.display = 'none';
            resultBox.style.display = 'block';
            
            document.getElementById('statusMsg').innerText = "Mã: " + decodedText;

            fetch('../../pages/api/process_qr.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ qr_code: decodedText })
            })
            .then(response => response.json())
            .then(data => {
                const icon = document.getElementById('statusIcon');
                const title = document.getElementById('statusTitle');
                
                if(data.success) {
                    icon.className = 'fa-solid fa-circle-check fa-3x text-success';
                    title.innerText = 'Thành Công!';
                    title.className = 'fw-bold text-success';
                    new Audio('https://www.soundjay.com/buttons/beep-01a.mp3').play();
                } else {
                    icon.className = 'fa-solid fa-circle-xmark fa-3x text-danger';
                    title.innerText = 'Lỗi!';
                    title.className = 'fw-bold text-danger';
                }
                document.getElementById('statusMsg').innerText = data.message;
            })
            .catch(err => {
                console.error(err);
                document.getElementById('statusTitle').innerText = 'Lỗi kết nối';
            });
        }

        let config = {
            fps: 10,
            qrbox: { width: 250, height: 250 }
        };

        let html5QrcodeScanner = new Html5QrcodeScanner("reader", config, false);
        html5QrcodeScanner.render(onScanSuccess);
    </script>
</body>
</html>  