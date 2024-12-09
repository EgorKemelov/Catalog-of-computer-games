<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Game</title>
    <link rel="stylesheet" href="style.css">
    <style>
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <a href='index.php'><img src="logo.png" alt="" class="logo"></a>
            <div class="buttons">
                <?php
                session_start([
                    'cookie_lifetime' => 2 * 3600,
                ]);
                if (isset($_SESSION['user'])) {
                    echo "<form action='Logout.php' method='post'><button type='submit' class='header__a'>Выйти</button></form>";
                } else {
                    echo "
                    <a href='Log_in.html' class='header__a'>Войти</a>
                    <a href='Sign_up.html' class='header__a'>Зарегистрироваться</a>";
                }
                ?>
            </div>
        </div>
        <?php
        require 'config.php';
        // Проверяем, передан ли параметр id
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id']; // Приводим к целому числу
            // Подготавливаем запрос для получения данных об игре
            $stmt = $connect->prepare("SELECT Game_name, Release_date, Discription, System_reauirements FROM Game WHERE id = ?");
            
            // Проверяем, успешно ли подготовлен запрос
            if ($stmt === false) {
                die('Ошибка подготовки запроса: ' . $connect->error);
            }
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $game = $result->fetch_assoc();
            
            // Проверяем, нашлась ли игра
           
            if ($game) {
                echo "
                <img src='images/' class='Eternal__Realms'>
                <h1>{$game['Game_name']}</h1>
                <p>{$game['Discription']}</p>
                <p>{$game['System_reauirements']}</p>
                <p>{$game['Release_date']}</p>";
                // Кнопка редактирования, видна только администратору
                if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] == 2) {
    echo "<form action='edit_game2.php' method='get'>
            <input type='hidden' name='id' value='{$id}'>
           <input type='submit' value='Редактировать' class='header___a' style='
    height: 40px;
    width: 150px; /* Задаем ширину кнопки */
    background: #13A3E8; /* Основной цвет */
    border: none; /* Убираем стандартную рамку */
    border-radius: 20px; /* Скругление углов */
    color: white; /* Цвет текста */
    font-size: 16px; /* Размер шрифта */
    font-weight: bold; /* Жирный шрифт */
    cursor: pointer; /* Курсор при наведении */
    position: relative;
    top: -8px;
    text-align: center; /* Центрирование текста */
    line-height: 10px; /* Вертикальное центрирование */
    transition: background 0.3s ease, transform 0.2s ease; /* Плавные переходы */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Тень для кнопки */
'>
          </form>";
}
            } else {
                echo "<p>Игра не найдена.</p>";
            }
        } else {
            echo "<p>ID не передан.</p>";
        }
        ?>
        <?php
        require 'comment.php';
        ?>
    </div>
</body>
</html>
