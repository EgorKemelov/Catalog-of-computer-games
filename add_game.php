<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить игру</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        h2 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
            position: absolute;
    margin-top: -774px;
        }
        form {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 500px; /* Увеличенная максимальная ширина формы */
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
        }
        input[type="text"],
        input[type="date"],
        textarea,
        select {
            width: calc(100% - 20px);
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #e7f3ff; /* Светло-голубой фон */
            color: #333;
            font-size: 16px;
            transition: border-color 0.3s ease; /* Плавный переход для цвета рамки */
        }
        input[type="text"]:focus,
        input[type="date"]:focus,
        textarea:focus,
        select:focus {
            border-color: #13A3E8; /* Цвет рамки при фокусе */
            outline: none; /* Убираем стандартный контур */
        }
        button {
            background-color: #13A3E8;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            transition: background-color 0.3s ease; /* Плавный переход для фона кнопки */
        }
        button:hover {
            background-color: #0c7bb9; /* Темнее при наведении */
        }
        .genre-select {
            height: auto; /* Автоматическая высота для многострочного выбора */
        }
    </style>
</head>
<body>

<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 2) {
    echo '<h2>Доступ запрещен.</h2>';
    exit;
}
?>

<h2>Добавить новую игру</h2>
<form action="add_game_process.php" method="post">
    <label for="Game_name">Название игры</label>
    <input type="text" name="Game_name" required placeholder="Введите название игры">

    <label for="Discription">Описание</label>
    <textarea name="Discription" required rows="4" placeholder="Введите описание игры"></textarea>

    <label for="Release_date">Дата релиза</label>
    <input type="date" name="Release_date" required>

    <label for="System_requirements">Системные требования</label>
    <textarea name="System_requirements" required rows="4" placeholder="Введите системные требования"></textarea>

    <label for="genre_ids">Жанры</label>
    <select name="genre_ids[]" class="genre-select" multiple required>
        <?php
        require_once('config.php');
        $genre_sql = "SELECT id, name FROM Jenre";
        $genre_result = $connect->query($genre_sql);
        
        while ($genre = $genre_result->fetch_assoc()) {
            echo '<option value="' . $genre['id'] . '">' . htmlspecialchars($genre['name']) . '</option>';
        }
        ?>
    </select>

    <button type="submit">Добавить игру</button>
</form>

</body>
</html>
