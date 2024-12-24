
<?php
// webhook.php
require_once('config.php');

// Получаем данные от Telegram
$content = file_get_contents("php://input");
$update = json_decode($content, true);

// Проверяем, получили ли мы данные
if (!$update) {
    exit("Ошибка: Нет данных");
}

// Извлекаем основные данные из update
$message = $update['message'] ?? null;
$chat_id = $message['chat']['id'] ?? null;
$text = $message['text'] ?? null;

// Проверяем наличие данных о сообщении
if ($chat_id && $text) {
    // Логика обработки команды или сообщения
    if (str_starts_with($text, '/start')) {
        $reply = "Добро пожаловать! Используйте /help, чтобы узнать доступные команды.";
    } elseif (str_starts_with($text, '/help')) {
        $reply = "Список доступных команд:\n/start - Начало работы\n/help - Помощь";
    } else {
        $reply = "Извините, я не понимаю эту команду. Попробуйте /help.";
    }

    // Отправляем ответное сообщение
    $url = "https://api.telegram.org/bot" . $bot_token . "/sendMessage";
    $data = [
        'chat_id' => $chat_id,
        'text' => $reply,
    ];

    // Используем cURL для отправки запроса
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    // Можно логировать ответ, если нужно
    // file_put_contents('log.txt', $response . PHP_EOL, FILE_APPEND);
}

// Завершаем скрипт
http_response_code(200);
exit("OK");
?>
