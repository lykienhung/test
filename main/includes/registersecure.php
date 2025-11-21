<?php
// includes/register_handler_secure.php

// Chỉ cho phép truy cập qua phương thức POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Lấy dữ liệu từ form
    $username = $_POST["username"];
    $pwd = $_POST["pwd"];
    $email = $_POST["email"];

    try {
        // Nhúng file kết nối CSDL
        require_once 'db.inc.php';

        // ================== PHẦN QUAN TRỌNG NHẤT ==================
        // MÃ HÓA MẬT KHẨU TRƯỚC KHI LƯU
        // Sử dụng thuật toán BCRYPT, là tiêu chuẩn an toàn hiện nay.
        $options = [
            'cost' => 12 // Tăng độ khó cho việc giải mã, 12 là một giá trị tốt
        ];
        $hashedPwd = password_hash($pwd, PASSWORD_BCRYPT, $options);
        // ==========================================================

        // Chuẩn bị câu lệnh SQL với các tham số ảo (?) để chống SQL Injection
        $query = "INSERT INTO users (username, pwd, email) VALUES (?, ?, ?);";
        
        $stmt = $pdo->prepare($query);
        
        // Thực thi câu lệnh, truyền vào các biến an toàn
        // Lưu ý: Chúng ta lưu $hashedPwd, KHÔNG PHẢI $pwd
        $stmt->execute([$username, $hashedPwd, $email]);

        // Đóng kết nối và giải phóng tài nguyên
        $pdo = null;
        $stmt = null;

        // Chuyển hướng người dùng về trang đăng nhập với thông báo thành công
        header("Location: ../login.php?signup=success");
        die();

    } catch (PDOException $e) {
        // Nếu có lỗi, dừng chương trình và hiển thị lỗi
        // Trong môi trường thực tế, bạn nên log lỗi thay vì hiển thị ra cho người dùng
        die("Query failed: " . $e->getMessage());
    }

} else {
    // Nếu ai đó cố gắng truy cập file này trực tiếp, chuyển hướng họ về trang chủ
    header("Location: ../main/index.php");
    die();
}