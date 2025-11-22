<?php
// includes/db.inc.php

// Lấy thông tin từ biến môi trường
$host = getenv('SUPABASE_HOST');
$dbname = "postgres"; 
$dbusername = "postgres"; // Lưu ý: Nếu dùng Pooler đôi khi phải là 'postgres.tên_project'
$dbpassword = getenv('SUPABASE_PASSWORD');

// QUAN TRỌNG: Đổi port thành 6543
$port = "6543"; 

// Chuỗi kết nối
// Thêm sslmode=require để đảm bảo bảo mật
$dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require";

try {
    $pdo = new PDO($dsn, $dbusername, $dbpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Tắt dòng báo lỗi này đi nếu chạy thành công
    // echo "Kết nối thành công"; 
} catch (PDOException $e) {
    // Vẫn giữ dòng này để debug nếu cần
    die("Lỗi kết nối cụ thể là: " . $e->getMessage());
}