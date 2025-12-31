<?php
// backend/logout.php
require_once 'session.php';

// تسجيل الخروج
adminLogout();
return ;

// توجيه لصفحة تسجيل الدخول
header('Location: login.php');
exit();
?>