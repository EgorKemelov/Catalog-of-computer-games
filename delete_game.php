<?php
require_once('config.php');
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 2) {
    echo "Доступ запрещен.";
    exit;
}

if (isset($_GET['id'])) {
    $id = $connect->real_escape_string($_GET['id']);

    $sql = "DELETE FROM Game WHERE id='$id'";

    if ($connect->query($sql) === TRUE) {
        header('Location: index.php'); // Перенаправление на главную страницу
        exit;
    } else {
        echo "Ошибка: " . $connect->error;
    }
} else {
    echo "Не указан идентификатор игры.";
}

$connect->close();
?>