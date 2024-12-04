<?php
require_once('config.php');
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 2) {
    echo "Доступ запрещен.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = (int)$_POST['id'];

    $sql = "DELETE FROM Suggested_Game WHERE id = $id";
    if ($connect->query($sql) === TRUE) {
        echo "Игра отклонена.";
    } else {
        echo "Ошибка: " . $connect->error;
    }
}

$connect->close();
header('Location: manage_suggestions.php');
?>
