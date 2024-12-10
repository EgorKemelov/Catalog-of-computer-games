<?php
session_start();
require_once('config.php'); // Подключаем файл конфигурации для работы с базой данных

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user'])) {
    header("Location: Log_in.html");
    exit();
}

$userId = $_SESSION['user']['id'];
$sql = "SELECT Nickname, Email, Birthdate, Address FROM users WHERE id = ?";
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nickname = $_POST['nickname'] ?? '';
    $email = $_POST['email'] ?? '';
    $birthdate = $_POST['birthdate'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $address = $_POST['address'] ?? '';

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
        $updateFields[] = "Birthdate = ?";
        $params[] = $birthdate;
    }
    if (!empty($address)) {
        $updateFields[] = "Address = ?";
        $params[] = $address;
    }
    if (!empty($newPassword)) {
        $hashedPassword = hash('sha512', $newPassword);
        $updateFields[] = "Password = ?";
        $params[] = $hashedPassword;
    }

    if (count($updateFields) > 0) {
        $params[] = $userId;
        $updateSql = "UPDATE users SET " . implode(", ", $updateFields) . " WHERE id = ?";
        $updateStmt = $connect->prepare($updateSql);
        if ($updateStmt === false) {
            die("Ошибка подготовки запроса: " . $connect->error);
        }

        $types = str_repeat('s', count($params) - 1) . 'i';
        $updateStmt->bind_param($types, ...$params);
        $updateStmt->execute();
        $updateStmt->close();

        if (!empty($nickname)) {
            $_SESSION['user']['nickname'] = $nickname;
        }

        $_SESSION['message'] = "Изменения успешно сохранены!";
        header("Location: profile.php");
        exit();
    }
}

$connect->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="jquery-master/examples/lib/jquery-1.11.1.min.js" type="text/javascript"></script>
        <script src="jquery-master/jquery.fias.min.js" type="text/javascript"></script>
        <script src="script.js" type="text/javascript"></script>
        <style>
        body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 20px;
}

.container {
    max-width: 600px;
    margin: auto;
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

h1 {
    text-align: center;
    color: #333;
}

.alert {
    background-color: #dff0d8; /* Светло-зеленый */
    color: #3c763d; /* Темно-зеленый */
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 20px;
}

.input-field {
    width: calc(100% - 22px);
    padding: 12px;
    border-radius: 8px; /* Увеличенный радиус закругления */
    border: 2px solid #007bff; /* Синяя рамка */
    background-color: #f0f8ff; /* Очень светлый голубой фон */
    font-size: 16px; /* Увеличенный размер шрифта */
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Легкая тень */
    transition: border-color 0.3s ease, background-color 0.3s ease; /* Плавный переход для рамки и фона */
}

.input-field:focus {
    border-color: #0056b3; /* Темно-синий цвет рамки при фокусе */
    background-color: #e7f3ff; /* Светло-голубой фон при фокусе */
}

.submit-button {
    width: 100%;
    padding: 12px;
    border-radius: 8px;
    border: none;
    background-color: #007bff; /* Синий цвет кнопки */
    color: white;
    font-size: 16px;
    cursor: pointer; /* Указатель при наведении */
}

.submit-button:hover {
    background-color: #0056b3; /* Темнее синий при наведении */
}

.back-link {
    display: inline-block; /* Для правильного отображения */
    margin-top: 20px;
    text-align: center;
    text-decoration: none;
    color: white;
    background-color: #17a2b8; /* Цвет ссылки */
    padding: 10px 15px;
    border-radius: 8px;
}

.back-link:hover {
    background-color: #138496; /* Темнее при наведении */
}
        </style>
</head>
<body>
    <div class="container">
    <h1>Личный кабинет пользователя</h1>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert">
            <?php
            echo $_SESSION['message'];
            unset($_SESSION['message']);
            ?>
        </div>
    <?php endif; ?>

    <form method="post" action="">
        <p>
            <strong>Никнейм:</strong>
            <input type="text" name="nickname" value="<?php echo htmlspecialchars($user['Nickname']); ?>" class="input-field">
        </p>
        <p>
            <strong>Email:</strong>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['Email']); ?>" class="input-field">
        </p>
        <p>
            <strong>Год рождения:</strong>
            <input type="text" name="birthdate" value="<?php echo htmlspecialchars($user['Birthdate']); ?>" placeholder="Введите год (например, 1990)" class="input-field">
        </p>
        <p>
            <strong>Адрес:</strong>
            <input type="text" name="address" placeholder="Адрес" class="input-field">
        </p>
        <p>
            <strong>Новый пароль:</strong>
            <input type="password" name="new_password" placeholder="Введите новый пароль (если хотите сменить)" class="input-field">
        </p>
        <input type="submit" value="Сохранить изменения" class="submit-button">
    </form>
    <a href="index.php" class="back-link">Вернуться на главную страницу</a>
</div>
</body>
</html>
