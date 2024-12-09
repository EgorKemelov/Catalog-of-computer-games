<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $query = $_POST['query'];

    // Новый адрес для API ФИАС
    $url = "https://your-fias-api-url?query=" . urlencode($query);

    $response = file_get_contents($url);
    $data = json_decode($response, true);

    $addresses = [];
    if (!empty($data['addresses'])) {
        foreach ($data['addresses'] as $item) {
            $addresses[] = $item['full_address']; // Название поля может отличаться
        }
    }

    echo json_encode($addresses);
}
?>
