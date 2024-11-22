<?php
require_once('config.php');
$login = mysqli_real_escape_string($connect, trim($_POST['login']));
$pass = hash('sha512', trim($_POST['pass']));
if (empty($login) || empty($pass)) {
    echo "Заполните все поля";
} else {
    // Изменяем SQL-запрос, чтобы также получить поле Role
    $sql = "SELECT * FROM users WHERE Nickname = '$login' AND Password = '$pass' LIMIT 1";
    $result = $connect->query($sql);
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            session_start(['cookie_lifetime' => 7400]);
            $_SESSION['user']['login'] = $row['Nickname'];
            $_SESSION['user']['id'] = $row['id'];
            $_SESSION['user']['role'] = $row['Role']; // Добавляем роль пользователя в сессию
            
            
            header("Location: index.php");
            exit(); // Не забудьте завершить выполнение скрипта после редиректа
        }
    } else {
        echo "Нет такого пользователя";
    }
}


?>
