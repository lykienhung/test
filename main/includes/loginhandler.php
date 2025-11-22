<?php
// includes/loginhandler.php
session_start();

// Bật báo lỗi để debug (Quan trọng khi test trên Vercel)
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Lấy dữ liệu an toàn
    $username = $_POST["username"] ?? null;
    $pwd = $_POST["pwd"] ?? $_POST["password"] ?? null; // Đề phòng form đặt tên khác

    if (!$username || !$pwd) {
        die("Lỗi: Vui lòng nhập đầy đủ Tên đăng nhập và Mật khẩu.");
    }

    try {
        // 2. Kết nối Database (Dùng __DIR__ để fix lỗi đường dẫn trên Vercel)
        require_once __DIR__ . '/db.inc.php';

        // 3. Tìm user trong Database
        // Dùng :username thay vì ? để code rõ ràng hơn
        $query = "SELECT * FROM users WHERE username = :username";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':username' => $username]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // 4. Kiểm tra mật khẩu
        // Lưu ý: $user['pwd'] phải đúng tên cột trong database của bạn
        if ($user && password_verify($pwd, $user['pwd'])) {
            
            // --- ĐĂNG NHẬP THÀNH CÔNG ---
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_username"] = htmlspecialchars($user["username"]);
            $_SESSION["user_email"] = $user["email"];

            header("Location: ../index.php?login=success");
            exit();

        } else {
            // --- SAI MẬT KHẨU ---
            $_SESSION['login_error'] = "Incorrect username or password.";
            header("Location: ../login.php");
            exit();
        }

        // Đóng kết nối
        $pdo = null;
        $stmt = null;

    } catch (PDOException $e) {
        die("Lỗi hệ thống: " . $e->getMessage());
    }
} else {
    header("Location: ../login.php");
    exit();
}