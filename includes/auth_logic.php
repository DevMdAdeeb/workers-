<?php
// includes/auth_logic.php
if (isset($_POST['register'])) {
    $u = $_POST['username'];
    $p = password_hash($_POST['password'], PASSWORD_DEFAULT);
    try {
        $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)")->execute([$u, $p]);
        $success = "تم إنشاء الحساب بنجاح!";
    } catch (Exception $e) { $error = "اسم المستخدم موجود مسبقاً!"; }
}
if (isset($_POST['login'])) {
    $s = $pdo->prepare("SELECT * FROM users WHERE username=?"); $s->execute([$_POST['username']]); $u = $s->fetch();
    if ($u && password_verify($_POST['password'], $u['password'])) {
        $_SESSION['user_id'] = $u['id']; $_SESSION['username'] = $u['username'];
        logActivity($pdo, $u['id'], null, "تسجيل دخول", "المستخدم: " . $u['username']);
        header("Location: index.php"); exit();
    } else { $error = "خطأ في بيانات الدخول"; }
}
if (isset($_GET['logout'])) {
    if(isset($_SESSION['user_id'])) logActivity($pdo, $_SESSION['user_id'], null, "تسجيل خروج");
    session_destroy(); header("Location: index.php"); exit();
}
?>
