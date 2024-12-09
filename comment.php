<?php

require_once('config.php'); // Подключаем файл конфигурации
$connect = mysqli_connect($servername, $username, $password, $dbname);
if (!$connect) {
    die("Ошибка подключения: " . mysqli_connect_error());
}
// Проверяем, был ли отправлен комментарий
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    if (isset($_SESSION['user']['id'])) {
        $id_game = (int)$_GET['id'];
        $id_user = $_SESSION['user']['id'];
        $comm = mysqli_real_escape_string($connect, trim($_POST['comment']));
        
        if (!empty($comm)) {
            $sql = "INSERT INTO User_and_Game (id_game, id_user, Comment, Mark) VALUES (?, ?, ?, ?)";
            $stmt = $connect->prepare($sql);
            $Mark = 0;
            if ($stmt === false) {
                die('Ошибка подготовки запроса: ' . htmlspecialchars($connect->error));
            }
            $stmt->bind_param("iisi", $id_game, $id_user, $comm, $Mark);
            if ($stmt->execute()) {
                echo "<p>Комментарий успешно добавлен!</p>";
            } else {
                echo "<p>Ошибка при добавлении комментария: " . htmlspecialchars($stmt->error) . "</p>";
            }
        } else {
            echo "<p>Комментарий не может быть пустым.</p>";
        }
    } else {
        echo "<p>Вы должны быть авторизованы, чтобы оставить комментарий.</p>";
    }
}
// Получаем комментарии для текущей игры
$id_game = (int)$_GET['id'];
$sql = "SELECT ug.id AS comment_id, u.Nickname, ug.Comment, ug.id_user FROM User_and_Game ug 
        JOIN users u ON ug.id_user = u.id 
        WHERE ug.id_game = ?";
$stmt = $connect->prepare($sql);
if ($stmt === false) {
    die('Ошибка подготовки запроса: ' . htmlspecialchars($connect->error));
}
$stmt->bind_param("i", $id_game);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Комментарии</title>
</head>
<body>
    <form action="" method="post">
        <textarea name="comment" required placeholder="Введите ваш комментарий..." style='
    width: 281px; /* Занимает всю ширину контейнера */
    height: 100px; /* Высота поля ввода */
    border: 1px solid #ccc; /* Светлая рамка */
    border-radius: 4px; /* Скругление углов */
    padding: 10px; /* Отступы внутри поля */
    font-size: 16px; /* Размер шрифта */
    resize: none; /* Запрет изменения размера поля */
    transition: border-color 0.3s ease; /* Плавный переход цвета рамки */
'>
</textarea>

<input type="submit" class='comment' value="Оставить комментарий" style='
    background: #13A3E8; /* Основной цвет кнопки */
    color: white; /* Цвет текста */
    font-size: 16px; /* Размер шрифта */
    border: none; /* Убираем стандартную рамку */
    border-radius: 4px; /* Скругление углов */
    padding: 10px 20px; /* Отступы внутри кнопки */
    cursor: pointer; /* Курсор при наведении */
    position: relative;
    top: -16px;
    transition: background 0.3s ease, transform 0.2s ease; /* Плавные переходы */
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Тень для кнопки */
'>
    </form>
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='comm__block' style='position: relative; width: 300px; border: 1px solid; margin-bottom: 20px;'>";
            echo "<strong class='Nick'>{$row['Nickname']}:</strong> {$row['Comment']}";
            
            // Проверяем, авторизован ли пользователь и имеет ли он право на удаление комментария
            if (isset($_SESSION['user']['id'])) {
                // Проверяем, является ли пользователь администратором или автором комментария
                if ($_SESSION['user']['role'] == 2 || $_SESSION['user']['id'] == $row['id_user']) {
                    echo "<form action='delete_comment.php' method='post' style='display:inline;'>
                            <input type='hidden' name='comment_id' value='{$row['comment_id']}'>
                            <input type='hidden' name='game_id' value='{$id_game}'>
                            <button type='submit' style='background: #13A3E8;
    color: white;
    text-decoration: none;
    position: relative;
    /top: -4px;/
    border-radius: 11px;'>Удалить</button>
                          </form>";
                }
            }
            
            echo "</div>";
        }
    } else {
        echo "<p>Комментариев нет.</p>";
    }
    ?>
</body>
</html>
