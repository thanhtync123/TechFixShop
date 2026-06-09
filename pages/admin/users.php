<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: /TechFixPHP/pages/public_page/login.php');
    exit;
}

include '../../config/db.php';


if (isset($_GET['get_user_json'])) {
    header('Content-Type: application/json');
    $id = intval($_GET['get_user_json']);
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    
    if($data) unset($data['password']); 
    
    echo json_encode($data);
    exit; 
}


$msg = '';
$msgType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $id = intval($_POST['id']);
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $address = trim($_POST['address'] ?? '');
    $role = $_POST['role'] ?? 'customer';
    $email = trim($_POST['email'] ?? '');

    try {
        if ($id > 0) {
            if (!empty($password)) {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET name=?, phone=?, password=?, address=?, role=?, email=?, updated_at=NOW() WHERE id=?");
                $stmt->bind_param('ssssssi', $name, $phone, $hashed, $address, $role, $email, $id);
            } else {
                $stmt = $conn->prepare("UPDATE users SET name=?, phone=?, address=?, role=?, email=?, updated_at=NOW() WHERE id=?");
                $stmt->bind_param('sssssi', $name, $phone, $address, $role, $email, $id);
            }
            $action = "Cập nhật";
        } else {
            if (empty($password)) {
                throw new Exception("Vui lòng nhập mật khẩu cho tài khoản mới.");
            }
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, phone, password, address, role, email) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param('ssssss', $name, $phone, $hashed, $address, $role, $email);
            $action = "Thêm mới";
        }
        
        if ($stmt->execute()) {
            header("Location: users.php?status=success&action=$action");
            exit;
        } else {
            throw new Exception($stmt->error);
        }
    } catch (Exception $e) {
        $msg = "Lỗi: " . $e->getMessage();
        $msgType = 'danger';
    }
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    try {
        $check = $conn->query("SELECT COUNT(*) as cnt FROM bookings WHERE customer_id = $id OR technician_id = $id");
        $row = $check->fetch_assoc();
        if ($row['cnt'] > 0) {
            $msg = "Không thể xóa: Người dùng này đang có đơn hàng liên quan!";
            $msgType = 'warning';
        } else {
            $conn->query("DELETE FROM users WHERE id = $id");
            header("Location: users.php?status=success&action=Xóa");
            exit;
        }
    } catch (Exception $e) {
        $msg = "Lỗi xóa: " . $e->getMessage();
        $msgType = 'danger';
    }
}

if(isset($_GET['status']) && $_GET['status'] == 'success') {
    $msg = ($_GET['action'] ?? 'Thao tác') . ' thành công!';
    $msgType = 'success';
}

$result = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Người dùng - Admin</title>
    <link href="/TechFixPHP/assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', sans-serif; }
        .sidebar { min-height: 100vh; background: #343a40; color: white; }
        .sidebar a { color: rgba(255,255,255,.8); text-decoration: none; padding: 12px 20px; display: block; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar a:hover { background: #0d6efd; color: white; }
        .card-header { background: white; font-weight: bold; border-bottom: 2px solid #f0f0f0; }
        .editing-form { border: 2px solid #ffc107; box-shadow: 0 0 15px rgba(255, 193, 7, 0.2); }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        
        <div class="col-md-3 col-lg-2 sidebar p-0 collapse d-md-block">
            <?php include __DIR__ . '/template/sidebar.php'; ?>
        </div>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
                <h1 class="h2"><i class="fa-solid fa-users-gear"></i> Quản lý Tài khoản</h1>
            </div>

            <?php if (!empty($msg)): ?>
                <div class="alert alert-<?= $msgType ?> alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-circle-info"></i> <?= $msg ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="row">
                
                <div class="col-lg-4 mb-4">
                    <div class="card shadow-sm" id="formCard">
                        <div class="card-header text-primary" id="formTitle">
                            <i class="fa-solid fa-user-plus"></i> Thêm tài khoản mới
                        </div>
                        <div class="card-body">
                            <form action="users.php" method="post" id="userForm">
                                <input type="hidden" name="id" id="inpId" value="0">

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Họ và tên <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="inpName" class="form-control" required placeholder="Nguyễn Văn A">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Số điện thoại</label>
                                    <input type="text" name="phone" id="inpPhone" class="form-control" placeholder="09xxxxxxxx">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Email</label>
                                    <input type="email" name="email" id="inpEmail" class="form-control" placeholder="email@example.com">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Mật khẩu</label>
                                    <input type="password" name="password" id="inpPassword" class="form-control" placeholder="Nhập mật khẩu">
                                    <div id="passHelp" class="form-text small text-muted" style="display:none">Để trống nếu không muốn đổi mật khẩu.</div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Vai trò</label>
                                    <select name="role" id="inpRole" class="form-select">
                                        <option value="customer">Khách hàng</option>
                                        <option value="technical">Kỹ thuật viên</option>
                                        <option value="admin">Quản trị viên</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Địa chỉ</label>
                                    <textarea name="address" id="inpAddress" class="form-control" rows="2"></textarea>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" name="save" class="btn btn-primary fw-bold" id="btnSave">
                                        <i class="fa-solid fa-floppy-disk"></i> Lưu Thông Tin
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" id="btnCancel" style="display:none" onclick="resetForm()">
                                        Hủy bỏ / Thêm mới
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <i class="fa-solid fa-list-ul"></i> Danh sách tài khoản
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="userTable" class="table table-hover table-bordered align-middle">
                                    <thead class="table-light text-center">
                                        <tr>
                                            <th width="5%">ID</th>
                                            <th>Thông tin</th>
                                            <th>Vai trò</th>
                                            <th>Địa chỉ</th>
                                            <th width="15%">Tác vụ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (mysqli_num_rows($result) > 0): ?>
                                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                                <tr>
                                                    <td class="text-center fw-bold"><?= $row['id'] ?></td>
                                                    <td>
                                                        <div class="fw-bold"><?= htmlspecialchars($row['name']) ?></div>
                                                        <small class="text-muted"><i class="fa-solid fa-phone"></i> <?= htmlspecialchars($row['phone']) ?></small>
                                                        <?php if(!empty($row['email'])): ?>
                                                            <br><small class="text-muted"><i class="fa-regular fa-envelope"></i> <?= htmlspecialchars($row['email']) ?></small>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php 
                                                            $roleClass = 'bg-secondary';
                                                            if($row['role'] == 'admin') $roleClass = 'bg-danger';
                                                            if($row['role'] == 'technical') $roleClass = 'bg-warning text-dark';
                                                            if($row['role'] == 'customer') $roleClass = 'bg-success';
                                                        ?>
                                                        <span class="badge <?= $roleClass ?>"><?= ucfirst($row['role']) ?></span>
                                                    </td>
                                                    <td>
                                                        <small class="d-block text-truncate" style="max-width: 150px;" title="<?= htmlspecialchars($row['address']) ?>">
                                                            <?= htmlspecialchars($row['address']) ?>
                                                        </small>
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" 
                                                                class="btn btn-sm btn-warning" 
                                                                onclick="loadUserForEdit(<?= $row['id'] ?>)" 
                                                                title="Sửa">
                                                            <i class="fa-solid fa-pen"></i>
                                                        </button>
                                                        
                                                        <a href="orders.php?customer_id=<?= $row['id'] ?>" class="btn btn-sm btn-info text-white" title="Lịch sử">
                                                            <i class="fa-solid fa-clock-rotate-left"></i>
                                                        </a>

                                                        <?php if($row['id'] != $_SESSION['user']['id']): ?>
                                                            <a href="users.php?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" title="Xóa" onclick="return confirm('Xóa tài khoản này?');">
                                                                <i class="fa-solid fa-trash"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div> 
        </main>
    </div>
</div>

<script src="/TechFixPHP/assets/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        $('#userTable').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/vi.json' },
            columnDefs: [ { orderable: false, targets: [4] } ]
        });
    });

    function loadUserForEdit(id) {
        document.getElementById('formCard').scrollIntoView({ behavior: 'smooth' });
        
        const btnSave = document.getElementById('btnSave');
        const originalText = btnSave.innerHTML;
        btnSave.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang tải...';
        btnSave.disabled = true;

        fetch('users.php?get_user_json=' + id)
            .then(response => response.json())
            .then(data => {
                if (data) {
                    document.getElementById('inpId').value = data.id;
                    document.getElementById('inpName').value = data.name;
                    document.getElementById('inpPhone').value = data.phone;
                    document.getElementById('inpEmail').value = data.email || '';
                    document.getElementById('inpAddress').value = data.address || '';
                    document.getElementById('inpRole').value = data.role;
                    
                    document.getElementById('inpPassword').value = '';
                    
                    document.getElementById('formTitle').innerHTML = '<i class="fa-solid fa-pen"></i> Cập nhật: ' + data.name;
                    document.getElementById('formCard').classList.add('editing-form');
                    document.getElementById('passHelp').style.display = 'block';
                    document.getElementById('btnCancel').style.display = 'inline-block';
                    btnSave.innerHTML = '<i class="fa-solid fa-check"></i> Cập nhật ngay';
                    btnSave.classList.remove('btn-primary');
                    btnSave.classList.add('btn-warning');
                }
            })
            .catch(err => console.error(err))
            .finally(() => {
                btnSave.disabled = false;
            });
    }

    function resetForm() {
        document.getElementById('userForm').reset();
        document.getElementById('inpId').value = 0;
        
        document.getElementById('formTitle').innerHTML = '<i class="fa-solid fa-user-plus"></i> Thêm tài khoản mới';
        document.getElementById('formCard').classList.remove('editing-form');
        
        document.getElementById('passHelp').style.display = 'none';
        document.getElementById('btnCancel').style.display = 'none';
        
        const btnSave = document.getElementById('btnSave');
        btnSave.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Lưu Thông Tin';
        btnSave.classList.add('btn-primary');
        btnSave.classList.remove('btn-warning');
    }
</script>

</body>
</html>