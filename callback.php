<?php
// Конфигурация приложения VK
$app_id = 'ВАШ_APP_ID'; // Замените на ваш ID приложения
$app_secret = 'ВАШ_СЕКРЕТНЫЙ_КЛЮЧ'; // Замените на ваш секретный ключ
$redirect_uri = 'http://ваш-домен/callback.php'; // URL для редиректа

// Подключение к базе данных
require_once('config.php');
$connect = mysqli_connect($servername, $username, $password, $username);

if (!$connect) {
    die("Ошибка подключения к базе данных: " . mysqli_connect_error());
}

if (isset($_GET['code'])) {
    $code = $_GET['code'];

    // Обмен кода на access_token
    $token_url = 'https://oauth.vk.com/access_token?' . http_build_query([
        'client_id'     => $app_id,
        'client_secret' => $app_secret,
        'redirect_uri'  => $redirect_uri,
        'code'          => $code,
    ]);

    $response = file_get_contents($token_url);
    $data = json_decode($response, true);

    if (isset($data['access_token'])) {
        $access_token = $data['access_token'];
        $user_id = $data['user_id'];
        $email = isset($data['email']) ? $data['email'] : null;

        // Запрос на получение данных о пользователе
        $user_info_url = 'https://api.vk.com/method/users.get?' . http_build_query([
            'user_ids'       => $user_id,
            'fields'         => 'first_name,last_name,bdate',
            'access_token'   => $access_token,
            'v'              => '5.131',
        ]);

        $user_response = file_get_contents($user_info_url);
        $user_info = json_decode($user_response, true);

        if (isset($user_info['response'][0])) {
            $user = $user_info['response'][0];
            $nickname = $user['first_name'] . ' ' . $user['last_name'];
            $birthdate = isset($user['bdate']) ? substr($user['bdate'], -4) : null; // Извлекаем год рождения
            $hashed_password = hash('sha512', $access_token); // Генерация пароля из токена

            // Проверка, существует ли пользователь в базе данных
            $check_user_query = "SELECT * FROM users WHERE Nickname = '$nickname'";
            $check_user_result = mysqli_query($connect, $check_user_query);

            if (mysqli_num_rows($check_user_result) > 0) {
                // Пользователь найден — авторизация
                session_start();
                $_SESSION['user'] = $nickname;

                header('Location: /profile.php'); // Перенаправление в личный кабинет
                exit();
            } else {
                // Пользователя нет, выполняем регистрацию
                $role = 1; // Роль по умолчанию
                $insert_user_query = "INSERT INTO users (Nickname, Password, Email, Birthdate, Role) 
                                      VALUES ('$nickname', '$hashed_password', '$email', '$birthdate', '$role')";
                if (mysqli_query($connect, $insert_user_query)) {
                    session_start();
                    $_SESSION['user'] = $nickname;

                    header('Location: /profile.php'); // Перенаправление в личный кабинет
                    exit();
                } else {
                    echo "Ошибка при регистрации: " . mysqli_error($connect);
                }
            }
        } else {
            echo 'Не удалось получить данные о пользователе.';
        }
    } else {
        echo 'Ошибка авторизации: не удалось получить access_token.';
    }
} else {
    echo 'Ошибка: код авторизации не был передан.';
}

// Закрываем подключение к базе данных
mysqli_close($connect);
