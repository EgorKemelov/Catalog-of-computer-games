<?php
require_once('config.php');

function verifyTelegramData($auth_data, $bot_token) {
    $check_hash = $auth_data['hash'];
    unset($auth_data['hash']);
    
    $data_check_string = '';
    foreach ($auth_data as $key => $value) {
        $data_check_string .= "$key=$value\n";
    }
    $data_check_string = trim($data_check_string);
    $secret_key = hash('sha256', $bot_token, true);
    $hash = hash_hmac('sha256', $data_check_string, $secret_key);
    
    return hash_equals($hash, $check_hash);
}

$bot_token = "YOUR_BOT_TOKEN"; // Замените на токен вашего бота
$auth_data = $_GET;

if (verifyTelegramData($auth_data, $bot_token)) {
    $telegram_id = $auth_data['id'];
    $first_name = $auth_data['first_name'];
    $last_name = $auth_data['last_name'];

    // Проверяем, существует ли пользователь в базе данных
    $sql = "SELECT * FROM users WHERE telegram_id = '$telegram_id' LIMIT 1";
    $result = $connect->query($sql);

    if ($result->num_rows > 0) {
        // Авторизация существующего пользователя
        session_start(['cookie_lifetime' => 7400]);
        $user = $result->fetch_assoc();
        $_SESSION['user']['login'] = $user['Nickname'];
        $_SESSION['user']['id'] = $user['id'];
        $_SESSION['user']['role'] = $user['Role'];
        header("Location: index.php");
        exit();
    } else {
        // Если пользователя нет, создаем запись
        $sql = "INSERT INTO users (Nickname, telegram_id, Role) VALUES ('$first_name', '$telegram_id', 'user')";
        if ($connect->query($sql) === TRUE) {
            session_start(['cookie_lifetime' => 7400]);
            $_SESSION['user']['login'] = $first_name;
            $_SESSION['user']['id'] = $connect->insert_id;
            $_SESSION['user']['role'] = 'user';
            header("Location: index.php");
            exit();
        } else {
            echo "Ошибка регистрации пользователя";
        }
    }
} else {
    echo "Ошибка проверки данных Telegram";
}
?>
