<?php
$host = 'localhost';
$db   = 'sams_workers';
$user = 'sams_workers';
$pass = 'Mohammed7134';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->exec("set names utf8mb4");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("خطأ اتصال");
}
?>
