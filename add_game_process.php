<?php
require_once('config.php');
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 2) {
    echo "Доступ запрещен.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $Game_name = $connect->real_escape_string($_POST['Game_name']);
    $Discription = $connect->real_escape_string($_POST['Discription']);
    $Release_date = $connect->real_escape_string($_POST['Release_date']);
    $System_reauirements = $connect->real_escape_string($_POST['System_reauirements']);

    $sql = "INSERT INTO Game (Discription, Release_date, Game_name, System_reauirements) VALUES ( '$Discription','$Release_date', '$Game_name', '$System_reauirements')";

    if ($connect->query($sql) === TRUE) {
        // Перенаправление на главную страницу
        header('Location: index.php');
        exit; // Завершаем скрипт после перенаправления
    } else {
        echo "Ошибка: " . $sql . "<br>" . $connect->error;
    }
}

$connect->close();
?>