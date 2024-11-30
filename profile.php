<?php
session_start();
require_once('config.php'); // Подключаем файл конфигурации для работы с базой данных

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Проверка, если пользователь не авторизован, перенаправляем на страницу логина
if (!isset($_SESSION['user'])) {
    header("Location: Log_in.html");
    exit();
}

// Получаем данные пользователя из базы
$userId = $_SESSION['user']['id']; // Предполагается, что ID пользователя хранится в сессии
$sql = "SELECT Nickname, Email, Birthdate FROM users WHERE id = ?";
$stmt = $connect->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
} else {
    echo "Пользователь не найден.";
    exit();
}

$stmt->close();

// Обработка изменения данных
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nickname = $_POST['nickname'] ?? '';
    $email = $_POST['email'] ?? '';
    $birthdate = $_POST['birthdate'] ?? ''; // Здесь будет вводится только год
    $newPassword = $_POST['new_password'] ?? '';

    // Начинаем формировать обновляющий SQL-запрос
    $updateFields = [];
    $params = [];

    if (!empty($nickname)) {
        $updateFields[] = "Nickname = ?";
        $params[] = $nickname;
    }
    if (!empty($email)) {
        $updateFields[] = "Email = ?";
        $params[] = $email;
    }
    if (!empty($birthdate)) {
        $updateFields[] = "Birthdate = ?"; // Сохраняем только год
        $params[] = $birthdate;
    }
    if (!empty($newPassword)) {
        $hashedPassword = hash('sha512', $newPassword);
        $updateFields[] = "Password = ?";
        $params[] = $hashedPassword;
    }

    // Если есть поля для обновления
    if (count($updateFields) > 0) {
        $params[] = $userId; // Добавляем ID пользователя в параметры
        $updateSql = "UPDATE users SET " . implode(", ", $updateFields) . " WHERE id = ?";

        $updateStmt = $connect->prepare($updateSql);
        if ($updateStmt === false) {
            die("Ошибка подготовки запроса: " . $connect->error);
        }

        // Определяем типы параметров
        $types = str_repeat('s', count($params) - 1) . 'i'; // Все строки (s), потом ID (i)
        $updateStmt->bind_param($types, ...$params);
        $updateStmt->execute();
        $updateStmt->close();

        // Обновляем сессию, если никнейм изменен
        if (!empty($nickname)) {
            $_SESSION['user']['nickname'] = $nickname;
        }

        // Устанавливаем сообщение об успешном сохранении
        $_SESSION['message'] = "Изменения успешно сохранены!";
        header("Location: profile.php"); // Перенаправление на страницу профиля
        exit();
    }
}

// Закрываем соединение с базой данных
$connect->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Личный кабинет пользователя</h1>

        <!-- Отображение сообщения об успешном сохранении -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert">
                <?php
                echo $_SESSION['message'];
                unset($_SESSION['message']); // Удаляем сообщение после его отображения
                ?>
            </div>
        <?php endif; ?>

        <form method="post" action="">
            <p><strong>Никнейм:</strong> <input type="text" name="nickname" value="<?php echo htmlspecialchars($user['Nickname']); ?>" style="
    border-radius: 11px;
"></p>
            <p><strong>Email:</strong> <input type="email" name="email" value="<?php echo htmlspecialchars($user['Email']); ?>"style="
    border-radius: 11px;
"></p>
            <p><strong>Год рождения:</strong> <input type="text" name="birthdate" value="<?php echo htmlspecialchars($user['Birthdate']); ?>" placeholder="Введите год (например, 1990)"style="
    border-radius: 11px;
"></p>
            <p><strong>Новый пароль:</strong> <input type="password" name="new_password" placeholder="Введите новый пароль (если хотите сменить)"style="
    border-radius: 11px;
"></p>
            <input type="submit" value="Сохранить изменения"style="
    border-radius: 11px;
    border: none;
    color: white;
    background: #13A3E8;
">
        </form>
        <a href="index.php" style="
    color: #ffffff;
    background: #13A3E8;
    position: relative;
    top: 10px;
    text-decoration: none;
">Вернуться на главную страницу</a>
    </div>
</body>
</html>
