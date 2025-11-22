<?php
// includes/logouthandler.php

// Đặt thời gian sống của cookie về quá khứ (time() - 3600) để trình duyệt tự xóa nó
setcookie("user_username", "", time() - 3600, "/");
setcookie("user_id", "", time() - 3600, "/");

// Chuyển về trang chủ
header("Location: ../index.php");
exit();
?>