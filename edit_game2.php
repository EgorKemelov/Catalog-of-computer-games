<?php
require 'config.php';

// Проверяем, передан ли параметр id
if (isset($_GET['id'])) {
    $id = (int)$_GET['id']; // Приводим к целому числу

    // Подготавливаем запрос для получения данных об игре
    $stmt = $connect->prepare("SELECT Game_name, Release_date, Discription, System_reauirements FROM Game WHERE id = ?");

    // Проверяем, успешно ли подготовлен запрос
    if ($stmt === false) {
        die('Ошибка подготовки запроса: ' . htmlspecialchars($connect->error));
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $game = $result->fetch_assoc();

    // Проверяем, нашлась ли игра
    if ($game) {
        echo "
        <h1>{$game['Game_name']}</h1>
        <form action='update_game.php' method='post'>
            <input type='hidden' name='id' value='{$id}'>
            <label for='Game_name'>Название игры:</label>
            <input  style='background: #13A3E8;
    color: white;
    text-decoration: none;
    position: relative;
    /*top: 11px;*/
    border-radius: 4px;
    margin: 4px;
}' type='text' id='Game_name' name='Game_name' value='" . htmlspecialchars($game['Game_name']) . "' required><br>

            <label for='Discription'>Описание:</label>
            <textarea style='background: #13A3E8;
    color: white;
    text-decoration: none;
    position: relative;
    /*top: 11px;*/
    border-radius: 4px;
}' id='Discription' name='Discription' required>" . htmlspecialchars($game['Discription']) . "</textarea><br>

            <label for='System_reauirements'>Системные требования:</label>
            <input type='text'style='background: #13A3E8;
    color: white;
    text-decoration: none;
    position: relative;
    /*top: 11px;*/
    border-radius: 4px;
}' id='System_reauirements' name='System_reauirements' value='" . htmlspecialchars($game['System_reauirements']) . "' required><br>

            <label for='Release_date' style='position: relative;
    top: 4px;'>Дата выхода:</label>
            <input style='background: #13A3E8;
    color: white;
    text-decoration: none;
    position: relative;
    top: 4px;
    border-radius: 4px;
}' type='date' id='Release_date' name='Release_date' value='" . htmlspecialchars($game['Release_date']) . "' required><br>

            <input type='submit' value='Обновить игру' style='background: #13A3E8;
    color: white;
    text-decoration: none;
    position: relative;
    top: 11px;
    border-radius: 4px;'>
        </form>"
         ;
    } else {
        echo "<p>Игра не найдена.</p>";
    }
} else {
    echo "<p>ID не передан.</p>";
}
?>
<a href="index.php">Отмена</a>