<?php
$app_id = '52746062';
$redirect_uri = 'https://ib.kocmic.ru/kemelov.e.s/callback.php'; // Указываем HTTPS

$auth_url = 'https://oauth.vk.com/authorize?' . http_build_query([
    'client_id'     => $app_id,
    'display'       => 'page',
    'redirect_uri'  => $redirect_uri,
    'scope'         => 'email',
    'response_type' => 'code',
    'v'             => '5.131',
]);

header('Location: ' . $auth_url);
exit();
