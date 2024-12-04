<?php
require_once('config.php');
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 2) {
    echo "Доступ запрещен.";
    exit;
}

$sql = "SELECT sg.id, sg.Game_name, sg.Discription, sg.Release_date, sg.System_reauirements, u.login 
        FROM Suggested_Game sg
        JOIN Users u ON sg.user_id = u.id
        WHERE sg.status = 'pending'";
$result = $connect->query($sql);

echo "<h2>Предложения игр</h2>";
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div>
            <p>Название: {$row['Game_name']}</p>
            <p>Описание: {$row['Discription']}</p>
            <p>Дата релиза: {$row['Release_date']}</p>
            <p>Системные требования: {$row['System_reauirements']}</p>
            <p>Предложено пользователем: {$row['login']}</p>
            <form action='approve_game.php' method='post' style='display:inline;'>
                <input type='hidden' name='id' value='{$row['id']}'>
                <button type='submit'>Утвердить</button>
            </form>
            <form action='reject_game.php' method='post' style='display:inline;'>
                <input type='hidden' name='id' value='{$row['id']}'>
                <button type='submit'>Отклонить</button>
            </form>
        </div><hr>";
    }
} else {
    echo "Нет предложений.";
}

$connect->close();
?>
