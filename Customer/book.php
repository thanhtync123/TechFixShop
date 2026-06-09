<?php
session_start();
require_once '../config/db.php'; 

if (!isset($_SESSION['user']) || ($_SESSION['role'] ?? null) !== 'customer') {
    header("Location: /TechFixPHP/pages/public_page/login.php");
    exit();
}

$user_session = $_SESSION['user'] ?? null; 
$customer_id = $user_session['id'] ?? ''; 
$customer_name = $user_session['name'] ?? ''; 
$customer_phone = $user_session['phone'] ?? ''; 
$customer_address = $user_session['address'] ?? ''; 

$services = [];
try {
    $result_services = $conn->query("SELECT id, name, price FROM services ORDER BY name ASC");
    if ($result_services) {
        $services = $result_services->fetch_all(MYSQLI_ASSOC); 
    }
} catch (Exception $e) {
    error_log("Lỗi lấy services (book.php): " . $e->getMessage());
}

$provinces = [];
try {
    $result_provinces = $conn->query("SELECT province_code, name FROM provinces ORDER BY name ASC");
    if ($result_provinces) {
        $provinces = $result_provinces->fetch_all(MYSQLI_ASSOC); 
    }
} catch (Exception $e) {
    error_log("Lỗi lấy provinces: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt Lịch Thông Minh - TECHFIX</title>
    <link href="/TechFixPHP/assets/css/book.css" rel="stylesheet" />
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        .toast { position: fixed; bottom: 20px; right: 20px; background: #333; color: #fff; padding: 1rem 1.5rem; border-radius: 8px; opacity: 0; transition: opacity 0.4s; z-index: 9999; }
        .toast.show { opacity: 1; }
        #smart-results { margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; }
        #price-notes { background: #fdfae5; padding: 15px; border-radius: 5px; border: 1px solid #e7d8a2; list-style-position: inside; }
        .slot { display: flex; justify-content: space-between; align-items: center; padding: 15px; border: 1px solid #ddd; margin: 10px 0; border-radius: 5px; }
        .slot.disabled { background: #f1f1f1; text-decoration: line-through; color: #999; }
        .slot-info strong { font-size: 1.1em; color: #007bff; }
        .slot-info span { display: block; font-size: 0.9em; color: #e67e22; }
        .slot button { background: #28a745; color: white; border: none; padding: 10px 15px; border-radius: 5px; cursor: pointer; font-weight: bold; }
        .slot.disabled button { background: #ccc; cursor: not-allowed; }
        .modal { display: none; position: fixed; z-index: 10000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.6); justify-content: center; align-items: center; }
        .modal-content { background-color: #fefefe; margin: auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 900px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.2); position: relative; animation-name: animatetop; animation-duration: 0.4s }
        @keyframes animatetop { from {top: -300px; opacity: 0} to {top: 0; opacity: 1} }
        .close-button { color: #aaa; float: right; font-size: 28px; font-weight: bold; position: absolute; top: 10px; right: 20px; cursor: pointer; }
        .close-button:hover, .close-button:focus { color: black; text-decoration: none; cursor: pointer; }
        .ai-diagnose-button { display: block; width: 100%; padding: 10px; margin-top: 10px; margin-bottom: 15px; background-color: #007bff; color: white; border: none; border-radius: 5px; font-size: 1rem; cursor: pointer; transition: background-color 0.3s ease; }
        .ai-diagnose-button:hover { background-color: #0056b3; }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="container flex justify-between items-center">
            <div class="flex items-center">
                <img src="../assets/image/VLUTE.png" alt="Logo" style="width:40px; height:60px; object-fit:contain; margin-right:8px;" />
                <h1 class="logo" style="margin:8px; line-height:60px;">TECHFIX</h1>
            </div>
            <div class="nav-links flex items-center space-x-4">
                <a href="../index.php">Trang Chủ |</a>
                <a href="Service.php">Dịch Vụ |</a>
                <a href="about.html">Về Chúng Tôi |</a>
                <a href="contact.html">Liên Hệ |</a>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        
        <div class="card">
            <div class="card-header">
                <h4>Đặt lịch dịch vụ thông minh</h4>
                <small>Giá và lịch trống sẽ cập nhật theo lựa chọn của bạn</small>
            </div>
            <div class="card-body">
                <form id="bookingForm" onsubmit="return false;">
                    
                    <h5>Bước 1: Thông tin của bạn</h5>
                    <div class="mb-2">
                        <label>Mã khách hàng</label>
                        <input type="text" id="idCustomer" value="<?php echo htmlspecialchars($customer_id); ?>" class="form-control"  readonly />
                    </div>
                    <div class="mb-2">
                        <label>Tên khách hàng</label>
                        <input type="text" id="customerName" value="<?php echo htmlspecialchars($customer_name); ?>" class="form-control" required />
                    </div>
                    <div class="mb-2">
                        <label>Số điện thoại</label>
                        <input type="text" id="phone" value="<?php echo htmlspecialchars($customer_phone); ?>" class="form-control" required />
                    </div>
                    <div class="mb-2">
                        <label>Địa chỉ (Nơi thực hiện dịch vụ)</label>
                        <input type="text" id="address" value="<?php echo htmlspecialchars($customer_address); ?>" class="form-control" required />
                    </div>


                    <h5 style="margin-top: 20px;">Bước 2: Lấy báo giá và lịch trống</h5>
                    <div class="mb-2">
                        <label>Tên dịch vụ</label>
                        <select id="serviceId" class="form-control" onchange="getSmartPrice()" required>
                            <option value="">-- Chọn dịch vụ --</option>
                            <?php foreach ($services as $service): ?>
                                <option value="<?php echo $service['id']; ?>">
                                    <?php 
                                        echo htmlspecialchars($service['name']) . 
                                             ' (từ ' . number_format($service['price'], 0, ',', '.') . 'đ)'; 
                                    ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button type="button" class="ai-diagnose-button" onclick="openAIDiagnoseModal()">
                        💡 Chẩn đoán lỗi bằng AI (Nếu bạn không chắc chắn)
                    </button>
                    <div class="mb-2">
                        <label>Tỉnh / Thành phố</label>
                        <select id="provinceId" class="form-control" onchange="loadDistricts()" required>
                            <option value="">-- Chọn Tỉnh/Thành --</option>
                            <?php foreach ($provinces as $province): ?>
                                <option value="<?php echo $province['province_code']; ?>">
                                    <?php echo htmlspecialchars($province['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label>Khu vực (Quận / Huyện)</label>
                        <select id="district" class="form-control" onchange="getSmartPrice()" required>
                            <option value="">-- Vui lòng chọn Tỉnh/Thành trước --</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label>Thời gian hẹn</label>
                        <input type="date" id="appointmentDate" class="form-control" onchange="getSmartPrice()" required />
                    </div>
                </form>

                <div id="smart-results">
                    <p style="text-align:center; color:#888;">Vui lòng chọn 3 mục trên để xem báo giá và lịch trống...</p>
                </div>
            </div>
        </div>

        <div class="services-column">
            <section id="services" class="section">
                <h2 class="section-title">Các Dịch Vụ TECHFIX</h2>
                <div class="slider-container">
                    <div class="slider">
                        <div class="slide-track" id="slideTrack">
                            <div class="slide">
                                <img src="../assets/image/car.jpg" alt="car">
                                <h3>Sửa Chữa & Bảo Trì Xe</h3>
                                <p>Dịch vụ sửa chữa và bảo trì xe chuyên nghiệp...</p>
                            </div>
                            <div class="slide">
                                <img src="../assets/image/pcc.jpg" alt="Sửa Chữa Máy Tính">
                                <h3>Sửa Chữa Máy Tính</h3>
                                <p>Sửa chữa máy tính từ phần cứng đến phần mềm...</p>
                            </div>
                            <div class="slide">
                                <img src="../assets/image/elec.jpg" alt="Electrical">
                                <h3>Sửa chữa & bảo trì hệ thống điện</h3>
                                <p>Dịch vụ điện dân dụng và công nghiệp toàn diện...</p>
                            </div>
                            <div class="slide">
                                <img src="../assets/image/air.jpg" alt="air-conditioned">
                                <h3>Sửa Chữa & Vệ Sinh Điện Lạnh</h3>
                                <p>Vệ sinh, sửa chữa và bảo trì hệ thống điện lạnh...</p>
                            </div>
                        </div>
                    </div>
                    <button class="control-btn" id="prevBtn">❮</button>
                    <button class="control-btn" id="nextBtn">❯</button>
                    <div class="pagination" id="pagination"></div>
                </div>

                <div class="more-btn-container" style="text-align: center; margin-top: 20px;">
                    <a href="services.php" class="more-btn">Tìm Hiểu Thêm</a>
                </div>

                <div class="company-info">
                    <img src="../assets/image/VLUTE.png" alt="Techfix Logo" class="company-logo" />
                    <div class="company-description">
                        <h3>Về TECHFIX</h3>
                        <p>TECHFIX cung cấp các giải pháp sáng tạo</p>
                    </div>
                    <div class="map-container">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d15705.130930698686!2d105.98417167672112!3d10.238765824299422!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sen!2s!4v1759656345208!5m2!1sen!2s"
                            width="600" height="450" style="border:0;" allowfullscreen loading="lazy"></iframe>
                    </div>
                </div>
            </section>
        </div>
    </div> 
    
    <div class="footer">
        <div>
            <h3>TECHFIX</h3>
            <p>ĐẶT NIỀM TIN - TRAO CHỮ TÍN</p>
        </div>
        <div>
            <h3>Liên Kết Nhanh</h3>
            <a href="#services">Dịch Vụ</a> |
            <a href="#about">Về Chúng Tôi</a> |
            <a href="#contact">Liên Hệ</a>
        </div>
        <div>
            <h3>Thông Tin Liên Hệ</h3>
            <p>Email: support@techfix.com</p>
            <p>Điện thoại: +84 123 456 789</p>
            <p>Địa chỉ: P4 Phạm Thái Bường</p>
        </div>
        <p class="copy">© 2025 TECHFIX. All rights reserved.</p>
    </div>
    <div id="toast" class="toast"></div>

    <div id="aiDiagnoseModal" class="modal">
        <div class="modal-content">
            <span class="close-button" onclick="closeAIDiagnoseModal()">×</span>
            <div id="aiDiagnoseContent">
                <p style="text-align:center;">Đang tải công cụ chẩn đoán AI...</p>
            </div>
        </div>
    </div>

    <script>
    function showToast(message, isError = false) {
        const toast = document.getElementById("toast");
        toast.textContent = message;
        toast.style.background = isError ? "#d9534f" : "#28a745";
        toast.classList.add("show");
        setTimeout(() => toast.classList.remove("show"), 3000);
    }

    function loadDistricts() {
        const provinceId = document.getElementById('provinceId').value;
        const districtSelect = document.getElementById('district');
        districtSelect.innerHTML = '<option value="">-- Đang tải... --</option>';
        document.getElementById('smart-results').innerHTML = '<p style="text-align:center; color:#888;">Vui lòng chọn 3 mục trên...</p>';
        if (!provinceId) {
            districtSelect.innerHTML = '<option value="">-- Vui lòng chọn Tỉnh/Thành trước --</option>';
            return;
        }
        fetch(`api_get_districts.php?province_id=${provinceId}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    districtSelect.innerHTML = '<option value="">-- Lỗi tải khu vực --</option>';
                    return;
                }
                if (data.length === 0) {
                    districtSelect.innerHTML = '<option value="">-- Không có khu vực nào --</option>';
                    return;
                }
                districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
                data.forEach(district => {
                    districtSelect.innerHTML += `<option value="${district.name}">${district.name}</option>`;
                });
            })
            .catch(error => {
                console.error('Lỗi tải khu vực:', error);
                districtSelect.innerHTML = '<option value="">-- Lỗi kết nối API --</option>';
            });
    }

    function getSmartPrice() {
        const serviceId = document.getElementById('serviceId').value;
        const district = document.getElementById('district').value; 
        const date = document.getElementById('appointmentDate').value;
        const resultsDiv = document.getElementById('smart-results');
        if (!serviceId || !district || !date) { 
            resultsDiv.innerHTML = '<p style="text-align:center; color:#888;">Vui lòng chọn 3 mục trên...</p>';
            return;
        }
        resultsDiv.innerHTML = '<p style="text-align:center; color:#007bff;">Đang kiểm tra giá và lịch trống...</p>';
        fetch(`api_price.php?service_id=${serviceId}&district=${district}&date=${date}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    resultsDiv.innerHTML = `<p style="color: red; text-align:center;">${data.error}</p>`;
                    return;
                }
                let html = `<h5>Bước 3: Chọn khung giờ</h5>`;
                if (data.price_notes && data.price_notes.length > 0) {
                    html += `<ul id="price-notes"><strong>Ghi chú điều chỉnh giá:</strong>`;
                    data.price_notes.forEach(note => html += `<li>${note}</li>`);
                    html += `</ul>`;
                }
                html += `<h5 style="margin-top:20px;">Các khung giờ khả dụng:</h5>`;
                let hasSlot = false;
                for (const time in data.available_slots) {
                    const slot = data.available_slots[time];
                    const price = slot.price.toLocaleString('vi-VN');
                    if (slot.available) {
                        hasSlot = true;
                        html += `<div class="slot"><div class="slot-info"><strong>${time}</strong><span style="color:green; font-weight:bold;">Giá: ${price}đ</span>${slot.note ? `<span>(${slot.note})</span>` : ''}</div><button onclick="bookSlot('${time}', ${slot.price})">Chọn và Đặt lịch</button></div>`;
                    } else {
                        html += `<div class="slot disabled"><div class="slot-info"><strong>${time}</strong><span>${slot.note}</span></div><button disabled>Đã hết</button></div>`;
                    }
                }
                if (!hasSlot) {
                    html += '<p style="text-align:center; color:red;">Rất tiếc, đã hết lịch cho ngày này. Vui lòng chọn ngày khác.</p>';
                }
                resultsDiv.innerHTML = html;
            })
            .catch(error => {
                console.error('Lỗi:', error);
                resultsDiv.innerHTML = "<p style='color: red; text-align:center;'>Không thể kết nối đến máy chủ API.</p>";
            });
    }

    function bookSlot(time, price) {
        const idCustomer = document.getElementById('idCustomer').value;
        const customerName = document.getElementById('customerName').value;
        const phone = document.getElementById('phone').value;
        const address = document.getElementById('address').value;
        const serviceId = document.getElementById('serviceId').value;
        const district = document.getElementById('district').value; 
        const date = document.getElementById('appointmentDate').value;

        if (!idCustomer || !customerName || !phone || !address) {
            Swal.fire({
                icon: 'warning',
                title: 'Thiếu thông tin',
                text: 'Vui lòng điền đầy đủ thông tin ở Bước 1.',
                confirmButtonColor: '#f39c12'
            });
            document.getElementById('customerName').focus();
            return;
        }

        Swal.fire({
            title: 'Xác nhận đặt lịch?',
            html: `Bạn muốn đặt lịch lúc <b>${time}</b><br>với giá ước tính: <b style="color:green; font-size: 1.2em;">${price.toLocaleString('vi-VN')}đ</b>?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Đồng ý đặt',
            cancelButtonText: 'Hủy bỏ',
            background: '#fff'
        }).then((result) => {
            if (result.isConfirmed) {
                
                Swal.fire({
                    title: 'Đang xử lý...',
                    text: 'Vui lòng chờ trong giây lát',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                const bookingData = { IdCustomer: idCustomer, CustomerName: customerName, Phone: phone, Address: address, District: district, ServiceId: serviceId, AppointmentDate: date, AppointmentTime: time, FinalPrice: price };
                
                fetch('submit_booking.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(bookingData)
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Thành công!',
                            text: result.message,
                            showConfirmButton: true,
                            confirmButtonText: 'OK',
                            timer: 5000
                        });
                        getSmartPrice();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Có lỗi xảy ra',
                            text: result.message
                        });
                    }
                })
                .catch(error => {
                    console.error('Lỗi khi gửi:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi kết nối',
                        text: 'Không thể gửi đơn đặt lịch. Vui lòng thử lại.'
                    });
                });
            }
        });
    }

    function openAIDiagnoseModal() {
        const modal = document.getElementById('aiDiagnoseModal');
        modal.style.display = 'flex'; 

        fetch('ai_diagnose_modal.php')
            .then(response => response.text())
            .then(html => {
                document.getElementById('aiDiagnoseContent').innerHTML = html;
                setupAIDiagnoseModal(); 
            })
            .catch(error => {
                console.error('Lỗi khi tải nội dung AI Modal:', error);
                document.getElementById('aiDiagnoseContent').innerHTML = '<p style="color:red;">Không thể tải công cụ chẩn đoán AI. Vui lòng thử lại sau.</p>';
            });
    }

    function closeAIDiagnoseModal() {
        const modal = document.getElementById('aiDiagnoseModal');
        modal.style.display = 'none'; 
    }

    window.onclick = function(event) {
        const modal = document.getElementById('aiDiagnoseModal');
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }
    
    function setupAIDiagnoseModal() {
        const aiFileUpload = document.getElementById('aiFileUpload');
        const uploadArea = document.getElementById('uploadArea');
        const previewArea = document.getElementById('previewArea');
        const diagnoseButton = document.getElementById('diagnoseButton');
        const loadingSpinner = document.getElementById('loadingSpinner');
        const diagnosisResult = document.getElementById('diagnosisResult');

        if (!uploadArea || !aiFileUpload || !previewArea || !diagnoseButton || !loadingSpinner || !diagnosisResult) {
             console.error('Không thể khởi tạo các element của AI modal.');
             return;
        }

        uploadArea.addEventListener('click', () => {
            aiFileUpload.click(); 
        });

        aiFileUpload.addEventListener('change', (event) => {
            const file = event.target.files[0];
            if (file) {
                previewArea.innerHTML = ''; 
                const fileType = file.type.split('/')[0];

                if (fileType === 'image') {
                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(file);
                    img.alt = 'Ảnh lỗi';
                    previewArea.appendChild(img);
                } else if (fileType === 'video') {
                    const video = document.createElement('video');
                    video.src = URL.createObjectURL(file);
                    video.controls = true;
                    video.alt = 'Video lỗi';
                    previewArea.appendChild(video);
                } else {
                    previewArea.innerHTML = '<p style="color:red;">File không phải ảnh hoặc video.</p>';
                    diagnoseButton.disabled = true;
                    previewArea.style.display = 'block';
                    return;
                }
                
                previewArea.style.display = 'block';
                diagnoseButton.disabled = false; 
                diagnosisResult.style.display = 'none'; 
            } else {
                previewArea.style.display = 'none';
                diagnoseButton.disabled = true;
            }
        });

        diagnoseButton.addEventListener('click', async () => {
            const file = aiFileUpload.files[0];
            if (!file) {
                alert("Vui lòng tải lên một ảnh hoặc video.");
                return;
            }

            diagnoseButton.disabled = true;
            loadingSpinner.style.display = 'block';
            diagnosisResult.style.display = 'none';
            diagnosisResult.innerHTML = ''; 

            const formData = new FormData();
            formData.append('media_file', file);

            try {
           
                const response = await fetch('api_ai_diagnose.php', { 
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                loadingSpinner.style.display = 'none';
                diagnosisResult.style.display = 'block';

                if (data.success) {
                   
                    const aiInfo = data.diagnosis; 

                    diagnosisResult.innerHTML = `
                        <p>🤖 AI chẩn đoán lỗi: <strong style="color:#007bff">${aiInfo.service_name}</strong></p>
                        <p><em>Lý do: ${aiInfo.description}</em></p>
                        <button class="select-service-button" onclick="selectDiagnosedService(${aiInfo.service_id})">
                            ✅ Chọn dịch vụ này ngay
                        </button>
                    `;
                } else {
                    diagnosisResult.innerHTML = `<p style="color:red;">${data.message || 'Lỗi khi chẩn đoán.'}</p>`;
                    if(data.debug) console.log(data.debug);
                }

            } catch (error) {
                console.error('Lỗi API AI:', error);
                loadingSpinner.style.display = 'none';
                diagnosisResult.style.display = 'block';
                diagnosisResult.innerHTML = '<p style="color:red;">Lỗi kết nối server. Vui lòng kiểm tra Console.</p>';
            } finally {
                diagnoseButton.disabled = false;
            }
        });
    }
    function selectDiagnosedService(serviceId) {
        document.getElementById('serviceId').value = serviceId; 
        closeAIDiagnoseModal(); 
        getSmartPrice(); 
        showToast("Dịch vụ đã được chọn tự động bởi AI!");
        document.getElementById('serviceId').focus();
    }

    document.addEventListener("DOMContentLoaded", () => {
        
        const track = document.getElementById("slideTrack");
        const slides = track ? track.children : [];
        const prevBtn = document.getElementById("prevBtn");
        const nextBtn = document.getElementById("nextBtn");
        const pagination = document.getElementById("pagination");
        let index = 0;

        if (pagination && slides.length > 0) {
            pagination.innerHTML = "";
            for (let i = 0; i < slides.length; i++) {
                const dot = document.createElement("span");
                dot.addEventListener("click", () => showSlide(i));
                pagination.appendChild(dot);
            }
        }
        function updatePagination() {
            if (!pagination) return; 
            [...pagination.children].forEach((dot, i) => {
                dot.classList.toggle("active", i === index);
            });
        }
        function showSlide(i) {
            if (!track) return; 
            if (slides.length === 0) return; 
            if (i < 0) index = slides.length - 1;
            else if (i >= slides.length) index = 0;
            else index = i;
            track.style.transform = `translateX(-${index * 100}%)`;
            updatePagination();
        }
        if (prevBtn && nextBtn) { 
            prevBtn.addEventListener("click", () => showSlide(index - 1));
            nextBtn.addEventListener("click", () => showSlide(index + 1));
        }
        if (slides.length > 0) { 
            setInterval(() => showSlide(index + 1), 5000);
            showSlide(0); 
        }
        
        const today = new Date().toISOString().split('T')[0];
        const dateInput = document.getElementById('appointmentDate');
        if (dateInput) { 
            dateInput.value = today;
            dateInput.min = today; 
        }
        
    });
</script>
</body>
</html>