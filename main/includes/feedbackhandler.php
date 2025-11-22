<?php
// includes/feedbackhandler.php

session_start();

// Bật hiển thị lỗi để debug trên Vercel (Tắt đi sau khi web đã chạy ngon)
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Lấy dữ liệu (Dùng ?? null cho an toàn)
    $name = $_POST["name"] ?? null;
    $email = $_POST["email"] ?? null;
    $message = $_POST["message"] ?? null;

    // 2. Validation cơ bản
    if (empty($name) || empty($email) || empty($message)) {
        $_SESSION['feedback_error'] = "Vui lòng điền đầy đủ tất cả các trường.";
        header("Location: ../index.php#feedback");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['feedback_error'] = "Định dạng email không hợp lệ.";
        header("Location: ../index.php#feedback");
        exit();
    }

    try {
        // 3. Kết nối CSDL (Dùng __DIR__ để fix lỗi đường dẫn Vercel)
        require_once __DIR__ . '/db.inc.php';

        // 4. Chuẩn bị câu lệnh SQL (Dùng :param cho an toàn và rõ ràng)
        // Lưu ý: Tên cột 'user_name', 'user_email' phải khớp trong Supabase
        $sql = "INSERT INTO feedback (user_name, user_email, message) VALUES (:name, :email, :message)";
        $stmt = $pdo->prepare($sql);

        // 5. Thực thi
        if ($stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':message' => $message
        ])) {
            // --- THÀNH CÔNG ---
            $_SESSION['feedback_success'] = "Cảm ơn bạn đã gửi phản hồi!";
            header("Location: ../index.php#feedback");
            exit();

        } else {
            echo "Lỗi: Không thể lưu vào database.";
        }

    } catch (PDOException $e) {
        // Hiện lỗi chi tiết để bạn biết sai ở đâu
        die("Lỗi hệ thống: " . $e->getMessage());
        
        // Sau này dùng code dưới để ẩn lỗi
        /*
        $_SESSION['feedback_error'] = "Đã có lỗi xảy ra, vui lòng thử lại sau.";
        header("Location: ../index.php#feedback");
        exit();
        */
    }

} else {
    // Nếu truy cập trực tiếp file này
    header("Location: ../index.php");
    exit();
}