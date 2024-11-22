<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем данные из формы
    $id = (int)$_POST['id'];
    $game_name = $_POST['Game_name'];
    $discription = $_POST['Discription'];
    $system_requirements = $_POST['System_reauirements'];
    $release_date = $_POST['Release_date'];

    // Подготавливаем запрос для обновления данных об игре
    $stmt = $connect->prepare("UPDATE Game SET Game_name = ?, Discription = ?, System_reauirements = ?, Release_date = ? WHERE id = ?");

    // Проверяем, успешно ли подготовлен запрос
    if ($stmt === false) {
        die('Ошибка подготовки запроса: ' . htmlspecialchars($connect->error));
    }

    // Связываем параметры и выполняем
    $stmt->bind_param("ssssi", $game_name, $discription, $system_requirements, $release_date, $id);

    if ($stmt->execute()) {
        echo "<p>Игра успешно обновлена!</p>";
        header("Location: game.php?id=$id");
        exit();
    } else {
        echo "<p>Ошибка обновления игры: " . htmlspecialchars($stmt->error) . "</p>";
    }
} else {
    echo "<p>Неверный метод запроса.</p>";
}
?>