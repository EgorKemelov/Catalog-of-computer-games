<html>
<head>
   <link rel="stylesheet" href="style.css">
   <style>
   /* Стили для контейнера */
.container {
    max-width: 800px; /* Максимальная ширина контейнера */
    margin: 0 auto; /* Центрирование контейнера */
    padding: 20px; /* Отступы внутри контейнера */
    background-color: #f9f9f9; /* Цвет фона */
    border-radius: 8px; /* Закругленные углы */
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Тень для эффекта глубины */
}

/* Стили для заголовка */
.header {
    text-align: center; /* Центрирование содержимого заголовка */
    margin-bottom: 20px; /* Отступ снизу */
}

/* Стили для логотипа */
.logo {
    max-width: 100%; /* Адаптивная ширина логотипа */
    height: auto; /* Автоматическая высота */
}

/* Стили для предложений игр */
h2 {
    text-align: center; /* Центрирование заголовка секции */
}

div > p {
    margin: 10px 0; /* Отступы между параграфами */
}

/* Стили для кнопок */
button {
    background-color: #007BFF; /* Синий фон кнопки */
    color: white; /* Белый текст */
    border: none; /* Убираем рамку */
    padding: 10px 15px; /* Отступы внутри кнопки */
    text-align: center; /* Центрирование текста в кнопке */
    text-decoration: none; /* Убираем подчеркивание текста */
    display: inline-block; /* Инлайн-блок для кнопок */
    margin-top: 10px; /* Отступ сверху от параграфов */
    cursor: pointer; /* Курсор при наведении на кнопку */
    border-radius: 4px; /* Закругленные углы кнопки */
}

button:hover {
    background-color: #0056b3; /* Темно-синий цвет кнопки при наведении */
}

   </style>
</head>
<body>
<div class="container">
        <div class="header">
            <a href='index.php'><img src="logo.png" alt="" class="logo"></a>
        </div>

        <?php
        require_once('config.php');
        session_start();

        // Проверяем, что пользователь существует и имеет роль 2
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 2) {
            echo "<p style='text-align:center;'>Доступ запрещен.</p>";
            exit;
        }

        $sql = "SELECT sg.id, sg.Game_name, sg.Discription, sg.Release_date, sg.System_reauirements, u.Nickname 
                FROM Suggested_Game sg
                JOIN users u ON sg.user_id = u.id
                WHERE sg.status = 'pending'";

        $result = $connect->query($sql);

        // Проверка на наличие ошибок при выполнении запроса
        if (!$result) {
            die("Ошибка выполнения запроса: " . $connect->error);
        }

        echo "<h2>Предложения игр</h2>";

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div>
                    <p>Название: {$row['Game_name']}</p>
                    <p>Описание: {$row['Discription']}</p>
                    <p>Дата релиза: {$row['Release_date']}</p>
                    <p>Системные требования: {$row['System_reauirements']}</p>
                    <p>Предложено пользователем: {$row['Nickname']}</p>
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
            echo "<p style='text-align:center;'>Нет предложений.</p>";
        }

        // Закрываем соединение с базой данных
        $connect->close();
        ?>
</div>
</body>
</html>
