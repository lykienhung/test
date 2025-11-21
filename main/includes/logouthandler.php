<?php

// includes/logouthandler.php

// Luôn bắt đầu session trước khi thao tác với nó
session_start();

// Xóa tất cả các biến session
session_unset();

// Hủy phiên làm việc hiện tại
session_destroy();

// Chuyển hướng người dùng về trang chủ
header("Location: ../index.php");
die();