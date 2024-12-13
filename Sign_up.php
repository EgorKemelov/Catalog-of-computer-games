<?php
require_once('config.php');
session_start();

// Проверка на POST-запрос
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login']);
    $password = trim($_POST['pass']);
    $repeatPassword = trim($_POST['repeatpass']);
    $email = trim($_POST['email']);
    $birthdate = trim($_POST['bdate']);

    if (empty($login) || empty($password) || empty($repeatPassword) || empty($email) || empty($birthdate)) {
        die("Заполните все поля");
    }

    if ($password !== $repeatPassword) {
        die("Пароли не совпадают");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Некорректный email");
    }

    // Проверка формата года рождения
    if (!preg_match('/^\d{4}$/', $birthdate)) {
        die("Некорректный формат года рождения");
    }

    // Хэширование пароля
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Подготовленный запрос для вставки данных
    $sql = "INSERT INTO users (Nickname, Password, Email, Birthdate) VALUES (?, ?, ?, ?)";
    $stmt = $connect->prepare($sql);
    if (!$stmt) {
        die("Ошибка подготовки запроса: " . htmlspecialchars($connect->error, ENT_QUOTES, 'UTF-8'));
    }

    $stmt->bind_param("ssss", $login, $hashedPassword, $email, $birthdate);
    if ($stmt->execute()) {
        echo "Успешная регистрация";
    } else {
        echo "Ошибка: " . htmlspecialchars($stmt->error, ENT_QUOTES, 'UTF-8');
    }
}
?>
