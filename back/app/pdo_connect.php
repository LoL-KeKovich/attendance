<?php
require_once 'redirect.php';
try {
    $pdo = new PDO("mysql:host=mysql;dbname=attendance_control;charset=utf8mb4", "user", "password");
} catch (PDOException $exception) {
    redirect_to_register();
}
