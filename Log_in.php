<?php
require_once('config.php');
session_start();

// Проверка на POST-запрос
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получение данных
    $login = trim($_POST['login']);
    $password = trim($_POST['pass']);

    if (empty($login) || empty($password)) {
        die("Заполните все поля");
    }

    // Подготовленный запрос
    $sql = "SELECT id, Nickname, Password, Role FROM users WHERE Nickname = ?";
    $stmt = $connect->prepare($sql);
    if (!$stmt) {
        die("Ошибка подготовки запроса: " . htmlspecialchars($connect->error, ENT_QUOTES, 'UTF-8'));
    }

    $stmt->bind_param("s", $login);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        // Проверка хэша пароля
        if (password_verify($password, $user['Password'])) {
            // Успешный вход
            $_SESSION['user'] = [
                'id' => $user['id'],
                'login' => $user['Nickname'],
                'role' => $user['Role'],
            ];
            header("Location: index.php");
            exit();
        }
    }
    // Неверные данные
    echo "Неверный логин или пароль";
}
?>
