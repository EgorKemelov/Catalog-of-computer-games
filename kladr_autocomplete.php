<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $query = $_POST['query'];

    $apiKey = "YOUR_API_KEY";
    $url = "https://kladr-api.ru/api.php?query=" . urlencode($query) . "&contentType=address&limit=10&token=$apiKey";

    $response = file_get_contents($url);
    $data = json_decode($response, true);

    $addresses = [];
    if (!empty($data['result'])) {
        foreach ($data['result'] as $item) {
            $addresses[] = $item['fullName'];
        }
    }

    echo json_encode($addresses);
}
