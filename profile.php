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
            <p><strong>Никнейм:</strong> <input type="text" name="nickname"  value="<?php echo htmlspecialchars($user['Nickname']); ?>" style="
    border-radius: 11px;
    background: #13A3E8;
    color: white;
"></p>
            <p><strong>Email:</strong> <input type="email" name="email" value="<?php echo htmlspecialchars($user['Email']); ?>" style="
    border-radius: 11px;
    background: #13A3E8;
    color: white;
"></p>
            <p><strong>Год рождения:</strong> <input type="text" name="birthdate" value="<?php echo htmlspecialchars($user['Birthdate']); ?>" placeholder="Введите год (например, 1990)" style="
    border-radius: 11px;
    background: #13A3E8;
    color: white;
"></p>
            <p><strong>Адрес:</strong> 
                <div id="one_string">
    <div class="input">
        <input type="text" name="address" placeholder="Адрес" style="
    border-radius: 11px;
    background: #13A3E8;
    color: white;
">
    </div>
</div>
            </p>
            <p><strong>Новый пароль:</strong> <input type="password" name="new_password" placeholder="Введите новый пароль (если хотите сменить)" style="
    border-radius: 11px;
    background: #13A3E8;
    color: white;
"></p>
            <input type="submit" value="Сохранить изменения" style="
    style=&quot;
    style=&quot;
    border-radius: 11px;
    background: #13A3E8;
    color: white;
&quot;;
    ;/* position: absolute; */;
    ;/* top: 10px; */;
    ;/* position: relative; */;
     border-radius: 11px; 
    ;/* color: black; */;
    text-decoration: none;
&quot;;
">
        </form>
        <a href="index.php" style="
    style=&quot;
    border-radius: 11px;
    background: #13A3E8;
    color: white;
&quot;;
    position: absolute;
    top: 10px;
    position: relative;
    /* border-radius: 11px; */
    color: black;
    text-decoration: none;
">Вернуться на главную страницу</a>
    </div>

</body>
</html>
