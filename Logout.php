<?php
session_start(); // Запускаем сессию

// Удаляем все переменные сессии
$_SESSION = [];

// Если необходимо удалить куки сессии, делаем это
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"], $params["secure"], $params["httponly"]
    );
}

// Уничтожаем сессию
session_destroy();

// Перенаправляем пользователя на страницу входа или главную страницу
header("Location: Log_in.html"); // Замените на нужный вам URL
exit();
?>