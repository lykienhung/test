<?php
// includes/db.inc.php

// Lấy thông tin từ biến môi trường của Vercel
$host = getenv('SUPABASE_HOST');
$dbname = "postgres"; // Tên mặc định luôn là postgres
$dbusername = "postgres";
$dbpassword = getenv('SUPABASE_PASSWORD'); // Quan trọng nhất
$port = "5432";

$dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require";

try {
    $pdo = new PDO($dsn, $dbusername, $dbpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Trên Vercel, lỗi sẽ hiện trong logs, không nên echo ra màn hình người dùng
    die("Connection failed. Check logs for details.");
}