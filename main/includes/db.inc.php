<?php
// includes/db.inc.php

// Host (Vercel Env Var hoặc hardcode tạm để test)
// Lưu ý: Nếu host trong Env Var của bạn vẫn là db.fwyryqq... thì cứ giữ nguyên,
// Supabase Pooler thông minh sẽ tự điều hướng.
$host = getenv('SUPABASE_HOST'); 

$dbname = "postgres";

// QUAN TRỌNG NHẤT: Sửa dòng này
$dbusername = "postgres.fwyryqqlohzstmdrjcwi"; 

$dbpassword = getenv('SUPABASE_PASSWORD');

// QUAN TRỌNG NHÌ: Port 6543
$port = "6543"; 

$dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require";

try {
    $pdo = new PDO($dsn, $dbusername, $dbpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Lỗi kết nối: " . $e->getMessage());
}