<?php session_start(); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST["username"];
    $pwd = $_POST["pwd"];

    try {
        require_once 'db.inc.php';

        $query = "SELECT * FROM users WHERE username = ?;";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $pdo = null;
        $stmt = null;
        
        // Luồng xử lý chính
        if ($user && password_verify($pwd, $user["pwd"])) {
            // Đăng nhập thành công, lưu session user
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_username"] = htmlspecialchars($user["username"]);
            $_SESSION["user_email"] = $user["email"];
            
            header("Location: ../index.php?login=success");
            die();
        } else {
            // SAI MẬT KHẨU HOẶC USERNAME
            // 1. Lưu thông báo lỗi vào session
            $_SESSION['login_error'] = "Incorrect username or password.";
            
            // 2. Chuyển hướng về trang login.php (không kèm lỗi trên URL)            
            header("Location: ../login.php");
            die();
        }

    } catch (PDOException $e) {
        // Lỗi hệ thống
        $_SESSION['login_error'] = "Đã có lỗi xảy ra, vui lòng thử lại sau.";
        header("Location: ../login.php");
        die();
    }

} else {
    header("Location: ../login.php");
    die();
}