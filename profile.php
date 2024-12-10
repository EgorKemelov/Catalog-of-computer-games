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

    // Валидация адреса через API КЛАДР
    $isValidAddress = false;
    if (!empty($address)) {
        $apiKey = "YOUR_API_KEY"; // Укажите ваш API-ключ для КЛАДР
        $url = "https://kladr-api.ru/api.php?query=" . urlencode($address) . "&contentType=address&limit=1&token=$apiKey";
        $response = file_get_contents($url);
        $data = json_decode($response, true);

        if (!empty($data['result']) && $data['result'][0]['fullName'] === $address) {
            $isValidAddress = true;
        }
    }

    if (!$isValidAddress) {
        $_SESSION['message'] = "Ошибка: Введённый адрес не найден в справочнике КЛАДР.";
        header("Location: profile.php");
        exit();
    }

    // Формирование запроса на обновление данных
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
