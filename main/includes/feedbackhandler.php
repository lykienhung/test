<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Lấy dữ liệu
    $name = $_POST["name"] ?? null;
    $email = $_POST["email"] ?? null;
    $message = $_POST["message"] ?? null;

    // 2. Validation cơ bản
    if (empty($name) || empty($email) || empty($message)) {
        // SỬA Ở ĐÂY: Dùng Cookie thay vì Session
        // Cookie sống 5 giây (time() + 5), đủ để hiện thông báo rồi tự mất
        setcookie('feedback_error', "Vui lòng điền đầy đủ tất cả các trường.", time() + 5, "/");
        
        header("Location: ../index.php#feedback");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // SỬA Ở ĐÂY:
        setcookie('feedback_error', "Định dạng email không hợp lệ.", time() + 5, "/");
        
        header("Location: ../index.php#feedback");
        exit();
    }

    try {
        require_once __DIR__ . '/db.inc.php';
        
        // ... (Đoạn code Insert SQL giữ nguyên) ...
        // Giả sử đoạn dưới này là đoạn Insert thành công:
        
        $sql = "INSERT INTO feedback (user_name, user_email, message) VALUES (:name, :email, :message)";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([':name' => $name, ':email' => $email, ':message' => $message])) {
            
            // SỬA ĐOẠN THÀNH CÔNG:
            setcookie('feedback_success', "Cảm ơn bạn! Chúng tôi đã nhận được tin nhắn.", time() + 5, "/");
            header("Location: ../index.php#feedback");
            exit();
            
        } else {
            setcookie('feedback_error', "Lỗi database, không gửi được.", time() + 5, "/");
            header("Location: ../index.php#feedback");
            exit();
        }

    } catch (PDOException $e) {
        setcookie('feedback_error', "Lỗi hệ thống: " . $e->getMessage(), time() + 5, "/");
        header("Location: ../index.php#feedback");
        exit();
    }

} else {
    // Nếu truy cập trực tiếp file này
    header("Location: ../index.php");
    exit();
}