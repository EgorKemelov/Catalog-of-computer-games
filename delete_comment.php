<?php
session_start();
require 'config.php'; // Подключаем файл с настройками базы данных
if (isset($_SESSION['user'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Получаем ID комментария и ID игры
        $comment_id = $_POST['comment_id'];
        $game_id = $_POST['game_id'];
        // Подготавливаем запрос для удаления комментария
        $stmt = $connect->prepare("DELETE FROM User_and_Game WHERE id = ?");
        $stmt->bind_param("i", $comment_id);
        
        if ($stmt->execute()) {
            echo "Комментарий успешно удален.";
        } else {
            echo "Ошибка при удалении комментария: " . htmlspecialchars($stmt->error);
        }
        $stmt->close();
    }
} else {
    echo "Вы должны быть авторизованы для удаления комментариев.";
}
$connect->close();
header("Location: game.php?id=" . $game_id); // Перенаправление обратно на страницу игры
exit();
?>
