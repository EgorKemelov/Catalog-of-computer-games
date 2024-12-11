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
        <div class='container'>
            <h1>{$game['Game_name']}</h1>
            <form action='update_game.php' method='post'>
                <input type='hidden' name='id' value='{$id}'>
                
                <div class='form-group'>
                    <label for='Game_name'>Название игры:</label>
                    <input class='styled-input' type='text' id='Game_name' name='Game_name' value='" . htmlspecialchars($game['Game_name']) . "' required>
                </div>

                <div class='form-group'>
                    <label for='Discription'>Описание:</label>
                    <textarea class='styled-input' id='Discription' name='Discription' required>" . htmlspecialchars($game['Discription']) . "</textarea>
                </div>

                <div class='form-group'>
                    <label for='System_reauirements'>Системные требования:</label>
                    <input class='styled-input' type='text' id='System_reauirements' name='System_reauirements' value='" . htmlspecialchars($game['System_reauirements']) . "' required>
                </div>

                <div class='form-group'>
                    <label for='Release_date'>Дата выхода:</label>
                    <input class='styled-input' type='date' id='Release_date' name='Release_date' value='" . htmlspecialchars($game['Release_date']) . "' required>
                </div>

                <input type='submit' class='styled-button' value='Обновить игру'>
            </form>
            
            <a href='index.php' class='styled-link'>Отмена</a>
        </div>

        <style>
            .container {
                max-width: 600px; /* Максимальная ширина контейнера */
                margin: 0 auto; /* Центрирование по горизонтали */
                padding: 20px; /* Отступы внутри контейнера */
                background-color: #f9f9f9; /* Цвет фона контейнера */
                border-radius: 8px; /* Скругление углов */
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Тень для эффекта глубины */
            }

            .form-group {
                margin-bottom: 15px; /* Отступ между группами */
            }

            .styled-input {
                width: 100%; /* Ширина 100% */
                background: #13A3E8;
                color: white;
                border: none;
                border-radius: 4px;
                padding: 10px; /* Внутренние отступы */
                font-size: 16px; /* Размер шрифта */
                transition: background 0.3s ease; /* Плавный переход */
            }

            .styled-input:focus {
                background: #0F7BBA; /* Цвет фона при фокусе */
                outline: none; /* Убираем стандартный контур */
            }

            .styled-button {
                background: #13A3E8;
                color: white;
                border-radius: 4px;
                border: none;
                padding: 10px 20px; /* Отступы */
                font-size: 16px; /* Размер шрифта */
                cursor: pointer; /* Указатель при наведении */
                transition: background 0.3s ease, transform 0.2s ease; /* Плавный переход */
            }

            .styled-button:hover {
                background: #0F7BBA; /* Цвет фона при наведении */
                transform: translateY(-2px); /* Легкий подъем кнопки */
            }

            .styled-button:active {
                transform: translateY(1px); /* Эффект нажатия */
            }

            .styled-link {
                display: inline-block; /* Позволяет использовать отступы и размеры */
                background: #13A3E8;
                color: white;
                border-radius: 3px;
                text-decoration: none;
                padding: 10px 20px; /* Отступы для кнопки */
                font-size: 16px; /* Размер шрифта */
                transition: background 0.3s ease, transform 0.2s ease; /* Плавный переход */
            }

            .styled-link:hover {
                background: #0F7BBA; /* Цвет фона при наведении */
                transform: translateY(-2px); /* Легкий подъем кнопки */
            }

            .styled-link:active {
                transform: translateY(1px); /* Эффект нажатия */
            }
        </style>
        ";
    } else {
        echo "<p>Игра не найдена.</p>";
    }
} else {
    echo "<p>ID не передан.</p>";
}
?>
