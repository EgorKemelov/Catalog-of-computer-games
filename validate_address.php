<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input = json_decode(file_get_contents('php://input'), true);
    $address = $input['address'] ?? '';

    if (empty($address)) {
        echo json_encode(['valid' => false, 'message' => 'Адрес не может быть пустым.']);
        exit;
    }

    // Проверяем адрес через API ФИАС
    $url = "https://your-fias-api-url?query=" . urlencode($address);
    $response = file_get_contents($url);
    $data = json_decode($response, true);

    if (!empty($data['addresses'])) {
        echo json_encode(['valid' => true]);
    } else {
        echo json_encode(['valid' => false, 'message' => 'Адрес не найден.']);
    }
}
?>
