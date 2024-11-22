<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить игру</title>
</head>
<body>
<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 2) {
    echo "Доступ запрещен.";
    exit;
}
?>

<h2>Добавить новую игру</h2>
<form action="add_game_process.php" method="post">
    <label for="Game_name">Название игры:</label>
    <input type="text" name="Game_name" required>
    <br>
    <label for="Discription">Описание:</label>
    <textarea name="Discription" required></textarea>
    <br>
    <label for="Release_date">Дата релиза:</label>
    <input type="date" name="Release_date" required>
    <br>
    <label for="System_reauirements">Системные требования:</label>
    <textarea name="System_reauirements" required></textarea>
    <br>
    <button type="submit">Добавить игру</button>
</form>

</body>
</html>