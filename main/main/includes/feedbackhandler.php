<?php
// includes/feedbackhandler.php

// Bắt đầu session để có thể gửi thông báo về trang chủ
session_start();

// Chỉ cho phép truy cập qua phương thức POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Lấy dữ liệu từ form và làm sạch cơ bản
    $name = htmlspecialchars($_POST["name"]);
    $email = htmlspecialchars($_POST["email"]);
    $message = htmlspecialchars($_POST["message"]);

    // --- Validation cơ bản ---
    if (empty($name) || empty($email) || empty($message)) {
        $_SESSION['feedback_error'] = "Vui lòng điền đầy đủ tất cả các trường.";
        header("Location: ../index.php#feedback"); // Quay lại section feedback
        die();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['feedback_error'] = "Định dạng email không hợp lệ.";
        header("Location: ../index.php#feedback");
        die();
    }
    // -------------------------

    try {
        // Nhúng file kết nối CSDL
        require_once 'db.inc.php';

        // Chuẩn bị câu lệnh SQL để chống SQL Injection
        $query = "INSERT INTO feedback (user_name, user_email, message) VALUES (?, ?, ?);";
        
        $stmt = $pdo->prepare($query);
        
        // Thực thi câu lệnh
        $stmt->execute([$name, $email, $message]);

        // Đóng kết nối
        $pdo = null;
        $stmt = null;

        // Lưu thông báo thành công vào session và chuyển hướng
        $_SESSION['feedback_success'] = "Cảm ơn bạn đã gửi phản hồi! Chúng tôi sẽ xem xét sớm nhất có thể.";
        header("Location: ../index.php#feedback");
        die();

    } catch (PDOException $e) {
        // Nếu có lỗi, lưu thông báo lỗi và chuyển hướng
        $_SESSION['feedback_error'] = "Đã có lỗi xảy ra, không thể gửi phản hồi. Vui lòng thử lại sau.";
        // Dòng dưới đây để debug, chỉ nên bật khi đang phát triển
        // $_SESSION['feedback_error'] = "Lỗi: " . $e->getMessage();
        header("Location: ../index.php#feedback");
        die();
    }

} else {
    // Nếu truy cập trực tiếp, đẩy về trang chủ
    header("Location: ../index.php");
    die();
}