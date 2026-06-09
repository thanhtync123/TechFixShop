<?php
session_start();
require_once 'config/db.php'; 


$client_id = ''; 
$client_secret = '';
$redirect_uri = 'http://localhost:8081/TechFixPHP/google_callback.php'; 

if (isset($_GET['code'])) {
    $token_url = 'https://oauth2.googleapis.com/token';
    $data = [
        'code' => $_GET['code'],
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'redirect_uri' => $redirect_uri,
        'grant_type' => 'authorization_code'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $token_url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
    
    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        die('Lỗi cURL: ' . curl_error($ch));
    }
    
    curl_close($ch);
    $token_data = json_decode($response, true);

    if (!isset($token_data['access_token'])) {
        die('Lỗi: Không lấy được Access Token từ Google.');
    }

    $user_info_url = 'https://www.googleapis.com/oauth2/v2/userinfo?access_token=' . $token_data['access_token'];
    
    $ch_info = curl_init();
    curl_setopt($ch_info, CURLOPT_URL, $user_info_url);
    curl_setopt($ch_info, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch_info, CURLOPT_SSL_VERIFYPEER, false);
    $info_response = curl_exec($ch_info);
    curl_close($ch_info);
    
    $user_info = json_decode($info_response, true);

    $google_id = $user_info['id'];
    $email = $user_info['email'];
    $name = $user_info['name']; 
    $picture = $user_info['picture'] ?? ''; 

    $stmt = $conn->prepare("SELECT * FROM users WHERE google_id = ? OR email = ?");
    $stmt->bind_param("ss", $google_id, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
   
        $update = $conn->prepare("UPDATE users SET google_id = ?, name = ? WHERE id = ?");
        $update->bind_param("ssi", $google_id, $name, $user['id']);
        $update->execute();

        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $name, 
            'email' => $email,
            'role' => $user['role'],
            'phone' => $user['phone'], 
            'address' => $user['address'] ?? '',
            'avatar' => $user['avatar'] ?? '',
            'picture' => $picture 
        ];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $name; 

    } else {
        $role = 'customer';
        
        $stmt = $conn->prepare(
            "INSERT INTO users (name, email, google_id, role, created_at) 
             VALUES (?, ?, ?, ?, NOW())"
        );
        $stmt->bind_param("ssss", $name, $email, $google_id, $role);
        
        if ($stmt->execute()) {
            $new_user_id = $conn->insert_id;
            
            $_SESSION['user'] = [
                'id' => $new_user_id,
                'name' => $name,
                'email' => $email,
                'role' => $role,
                'picture' => $picture
            ];
            $_SESSION['role'] = $role;
            $_SESSION['name'] = $name;
        } else {
            die("Lỗi tạo tài khoản: " . $conn->error);
        }
    }

    header("Location:index.php"); 
    
    exit();

} else {
    header("Location: pages/public_page/login.php");
    exit();
}
?>