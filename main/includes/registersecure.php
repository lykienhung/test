<?php
// includes/register_handler_secure.php

// Bật hiển thị lỗi hệ thống để debug (Xóa 2 dòng này khi đã chạy ngon)
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Lấy dữ liệu và kiểm tra (Dùng ?? null để tránh lỗi Undefined Index)
    // LƯU Ý: Kiểm tra kỹ bên file HTML xem name="username" hay name="uid"
    $username = $_POST["username"] ?? $_POST["uid"] ?? null; 
    $pwd = $_POST["pwd"] ?? $_POST["password"] ?? null;
    $email = $_POST["email"] ?? null;

    // Nếu thiếu dữ liệu thì dừng luôn
    if (!$username || !$pwd || !$email) {
        die("Lỗi: Dữ liệu gửi lên bị thiếu. Hãy kiểm tra lại thuộc tính name='' trong file HTML.");
    }

    try {
        // 2. Kết nối CSDL (Dùng __DIR__ để lấy đường dẫn tuyệt đối - Fix lỗi Vercel)
        require_once __DIR__ . '/db.inc.php';

        // 3. Mã hóa mật khẩu
        $options = ['cost' => 12];
        $hashedPwd = password_hash($pwd, PASSWORD_BCRYPT, $options);

        // 4. Câu lệnh SQL chuẩn PostgreSQL (Dùng :param thay vì ?)
        // "pwd" là tên cột trong Database bạn đã tạo
        $sql = "INSERT INTO users (username, pwd, email) VALUES (:username, :pwd, :email)";
        
        $stmt = $pdo->prepare($sql);

        // 5. Thực thi
        $stmt->execute([
            ':username' => $username,
            ':pwd'      => $hashedPwd,
            ':email'    => $email
        ]);

        // 6. Thông báo thành công
        // Tạm thời COMMENT dòng chuyển hướng để nhìn thấy kết quả trên màn hình
        echo "<h1>ĐĂNG KÝ THÀNH CÔNG!</h1>";
        echo "<p>Dữ liệu đã được lưu vào Supabase.</p>";
        echo "<a href='../login.php'>Bấm vào đây để đăng nhập</a>";
        
        // Sau khi test xong thì bỏ comment dòng dưới để tự chuyển hướng
        // header("Location: ../login.php?signup=success");
        // exit();

    } catch (PDOException $e) {
        // Bắt lỗi trùng lặp (Ví dụ: Username hoặc Email đã tồn tại)
        if ($e->getCode() == '23505') { // Mã lỗi 23505 là Unique Violation trong Postgres
            die("Lỗi: Username hoặc Email này đã có người dùng!");
        }
        die("Query failed: " . $e->getMessage());
    }
} else {
    header("Location: ../register.html");
    exit();
}