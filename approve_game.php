<?php
require_once('config.php');
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 2) {
    echo "Доступ запрещен.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = (int)$_POST['id'];

    // Переносим предложение в таблицу `Game`
    $sql = "INSERT INTO Game (Game_name, Discription, Release_date, System_reauirements)
            SELECT Game_name, Discription, Release_date, System_reauirements 
            FROM Suggested_Game WHERE id = $id";

    if ($connect->query($sql) === TRUE) {
        $connect->query("DELETE FROM Suggested_Game WHERE id = $id"); // Удаляем из предложений
        echo "Игра утверждена.";
    } else {
        echo "Ошибка: " . $connect->error;
    }
}

$connect->close();
header('Location: manage_suggestions.php');
?>
