<?php
require_once('config.php');
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 1) {
    echo "Доступ запрещен.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $Game_name = $connect->real_escape_string($_POST['Game_name']);
    $Discription = $connect->real_escape_string($_POST['Discription']);
    $Release_date = $connect->real_escape_string($_POST['Release_date']);
    $System_reauirements = $connect->real_escape_string($_POST['System_reauirements']);
    $user_id = $_SESSION['user']['id']; // ID пользователя, предлагающего игру

    $sql = "INSERT INTO Suggested_Game (Game_name, Discription, Release_date, System_reauirements, user_id, status) 
            VALUES ('$Game_name', '$Discription', '$Release_date', '$System_reauirements', $user_id, 'pending')";

    if ($connect->query($sql) === TRUE) {
        echo "Игра успешно предложена. Ожидайте проверки администратором.";
        header('Location: index.php');
        exit;
    } else {
        echo "Ошибка: " . $sql . "<br>" . $connect->error;
    }
}

$connect->close();
?>
