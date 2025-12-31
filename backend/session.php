<?php
// backend/session.php
session_start();

// إعدادات أمان الجلسات
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_samesite', 'Strict');

// التحقق من تسجيل الدخول
function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// إجبار تسجيل الدخول
function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

// تسجيل الدخول
function adminLogin($admin_id, $username) {
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_id'] = $admin_id;
    $_SESSION['admin_username'] = $username;
    $_SESSION['login_time'] = time();
}

// تسجيل الخروج
function adminLogout() {
    session_unset();
    session_destroy();
    session_start();
    session_regenerate_id(true);
}

// الحصول على معلومات المسؤول
function getAdminInfo() {
    if (isAdminLoggedIn()) {
        return [
            'id' => $_SESSION['admin_id'] ?? 0,
            'username' => $_SESSION['admin_username'] ?? ''
        ];
    }
    return null;
}
?>