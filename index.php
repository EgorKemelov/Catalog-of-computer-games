<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Главная</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php 
    session_start([
        'cookie_lifetime' => 2 * 3600,
    ]);
    if (isset($_SESSION['user'])) {
        echo "Привет, " . htmlspecialchars($_SESSION['user']['login']);
        if ($_SESSION['user']['role'] == 1) {
            echo "<p>Вы обычный пользователь.</p>";
        } elseif ($_SESSION['user']['role'] == 2) {
            echo "<p>Вы администратор.</p>";
        }
    } else {
        echo "<p>Вы не зарегистрированы. Пожалуйста, войдите или зарегистрируйтесь.</p>";
    }
    ?>
    <div class="container">
        <div class="header">
            <a href='index.php'><img src="logo.png" alt="" class="logo"></a>
            <div class="buttons">
            
            
                <?php
                if (isset($_SESSION['user']['login'])) {
                echo "<a href='profile.php' class='header__a'>Личный кабинет</a>"; // Ссылка на Личный кабинет
                    echo "<form action='Logout.php' method='post'><button type='submit' class='header__a'>Выйти</button></form>";
                    
                    
                } else {
                    echo "<a href='Log_in.html' class='header__a'>Войти</a>";
                    echo "<a href='Sign_up.html' class='header__a'>Зарегистрироваться</a>";
                }
                ?>
            </div>
        </div>
    </div>

    <form method="GET" action="">
        <input type="text" name="search" placeholder="Поиск игр..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" required style="
    padding: 10px 15px; /* Отступы внутри поля */
    border: 2px solid #13A3E8; /* Цвет границы */
    border-radius: 20px; /* Скругление углов */
    font-size: 16px; /* Размер шрифта */
    color: #333; /* Цвет текста */
    background-color: #f0f8ff; /* Цвет фона */
    width: 300px; /* Ширина поля ввода */
    transition: border-color 0.3s ease, box-shadow 0.3s ease; /* Плавные переходы для эффектов */
">

    
  <button type="submit" style="
    /*margin-left: 28px;*/
    padding: 10px 15px;
    background: #13A3E8;
    border: none;
    border-radius: 20px;
    color: white;
    font-size: 16px;
    font-weight: bold;
    text-decoration: none;
    cursor: pointer;
    transition: background 0.3s ease, transform 0.2s ease;
">
    Поиск
</button>

        <a href="index.php" style="
    margin-left: 10px; /* Отступ слева */
    padding: 10px 15px; /* Отступы внутри ссылки */
    background: #13A3E8; /* Цвет фона */
    border-radius: 20px; /* Скругление углов */
    text-decoration: none; /* Убираем подчеркивание текста */
    color: white; /* Цвет текста */
    font-weight: bold; /* Жирный шрифт для выделения */
    display: inline-block; /* Делаем ссылку блочным элементом для отступов */
    transition: background 0.3s ease, transform 0.2s ease; /* Плавные переходы для эффектов */
">
    Сбросить фильтрацию
</a>

        <!-- Форма для фильтрации по жанрам -->
        <select name="genre_id" onchange="this.form.submit()" style='margin-left: 10px; /* Отступ слева */
    padding: 10px 15px; /* Отступы внутри выпадающего списка */
    background: #13A3E8; /* Цвет фона */
    border: none; /* Убираем стандартную границу */
    border-radius: 20px; /* Скругление углов */
    color: white; /* Цвет текста */
    font-size: 16px; /* Размер шрифта */
    cursor: pointer; /* Курсор в виде руки при наведении */
    transition: background 0.3s ease, border-color 0.3s ease; /* Плавные переходы для эффектов */'>
            <option value="">Выберите жанр</option>
            <?php
            require_once('config.php');
            $genreQuery = "SELECT id, name FROM Jenre";
            $genreResult = $connect->query($genreQuery);

            while ($genre = $genreResult->fetch_assoc()) {
                $selected = (isset($_GET['genre_id']) && $_GET['genre_id'] == $genre['id']) ? 'selected' : '';
                echo "<option value=\"{$genre['id']}\" $selected>" . htmlspecialchars($genre['name']) . "</option>";
            }
            ?>
        </select>
    </form>

    <div class="frame">
        <?php
        // SQL запрос для получения игр с жанрами
        $search = isset($_GET['search']) ? $_GET['search'] : '';
        $selectedGenreId = isset($_GET['genre_id']) ? (int)$_GET['genre_id'] : 0;

        $sql = "
            SELECT g.id, g.Game_name, g.Discription, GROUP_CONCAT(j.name SEPARATOR ', ') AS genres
            FROM Game g
            LEFT JOIN Game_jenre gj ON g.id = gj.id_game
            LEFT JOIN Jenre j ON gj.id_jenre = j.id
        ";
        $conditions = [];
        $params = [];

        // Проверяем, задан ли поиск
        if ($search) {
            $conditions[] = "g.Game_name LIKE ?";
            $params[] = '%' . $search . '%';
        }

        // Проверяем, задан ли жанр
        if ($selectedGenreId > 0) {
            $conditions[] = "gj.id_jenre = ?";
            $params[] = $selectedGenreId;
        }

        // Если есть условия, добавляем WHERE
        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " GROUP BY g.id"; // Группировка по ID игры, чтобы объединить жанры в одну строку

        $stmt = $connect->prepare($sql);

        // Проверяем, есть ли параметры для привязки
        if ($params) {
            $types = '';
            foreach ($params as $param) {
                $types .= (is_int($param) ? 'i' : 's'); // 'i' для целых, 's' для строк
            }
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div id='g_{$row['id']}' class='game'>
                    <img src='images/{$row['id']}.png' class='Eternal__Realms'>
                    <p class='text' style='display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
    top: 38px;'>{$row['Game_name']}</p>
                    <p class='description' style='text-overflow: clip;
    align-items: center;
    justify-content: center;
    display: flex;
    position: relative;
    top: 20px;
    padding: 10px;'>{$row['Discription']}</p>
                    <p class='genres' style='position: relative;
    text-align: center;
    top: -32px;'>Жанры: {$row['genres']}</p> <!-- Отображение жанров -->
                    <a href='game.php?id={$row['id']}' class='page' style=' .page {
            margin-left: 28px; /* Отступ слева */
            display: flex; /* Используем flexbox для выравнивания */
            flex-direction: row; /* Горизонтальное расположение элементов */
            justify-content: center; /* Центрируем содержимое по горизонтали */
            align-items: center; /* Центрируем содержимое по вертикали */
            padding: 12px 20px; /* Уменьшенные отступы внутри кнопки */
            gap: 10px; /* Промежуток между элементами */
            background: #13A3E8; /* Цвет фона кнопки */
            border-radius: 20px; /* Скругление углов кнопки */
            text-decoration: none; /* Убираем подчеркивание текста ссылки */
            color: white; /* Цвет текста ссылки */
            width: 200px; /* Ширина кнопки */
            position: relative; /* Позиционирование относительно родителя */
            transition: background-color 0.3s ease, transform 0.2s ease; /* Плавные переходы для эффектов */
        }

        .page:hover {
            background-color: #0f7cba; /* Более темный оттенок фона при наведении */
            transform: scale(1.05); /* Немного увеличиваем кнопку при наведении */
        }'>Страничка с игрой</a>";

                // Проверяем, является ли пользователь администратором
                if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] == 2) {
                    echo "<a href='edit_game.php?id={$row['id']}' class='edit-button' style='background: #13A3E8; color: white; text-decoration: none; position: relative; top: -202px; border-radius: 4px; padding: 3px; left: 10px;'>Редактировать</a>
                    <a href='delete_game.php?id={$row['id']}' class='delete-button' style='background: red; color: white; text-decoration: none; margin-left: 10px; position: relative; top: -202px;' onclick='return confirm(\"Вы уверены, что хотите удалить эту игру?\");'>Удалить</a>";
                }
                echo "</div>";
            }
        } else {
            echo "Нет доступных игр.";
        }

        $stmt->close();
        $connect->close();
        ?>
    </div>

    <?php if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] == 2) { ?>
        <a href='add_game.php' class='add-game-button' style='background: #13A3E8; color: white; text-decoration: none; position: relative; /*top: -2630px;*/ border-radius: 10px; padding: 3px; margin-left: 575px'>Добавить новую игру</a>
    <?php } ?>
    <!-- Форма для предложения игры -->
    <?php if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] == 1) { ?>
        <a href="suggest_game.php" style="background: #13A3E8; color: white; text-decoration: none; padding: 10px; border-radius: 10px;">Предложить игру</a>
    <?php } ?>

    <!-- Раздел для предложенных игр -->
    <?php if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] == 2) { ?>
        <h2>Предложенные игры</h2>
        <div class="suggested-games">
            <?php
        $connect = mysqli_connect($servername, $username, $password, $dbname);
if(!$connect){
   die("connection Failed: " . mysqli_connect_error());
} else {
   echo "Подключение успешно!";
}
          $suggestedGamesQuery = "SELECT Game_name, Discription, Release_date, System_reauirements 
            FROM Suggested_Game";
            $suggestedGamesResult = $connect->query($suggestedGamesQuery);

            if ($suggestedGamesResult && $suggestedGamesResult->num_rows > 0) {
                while ($game = $suggestedGamesResult->fetch_assoc()) {
                    echo "<div class='suggested-game'>
                        <p>Название: {$game['Game_name']}</p>
                        <p>Описание: {$game['Discription']}</p>
                        <a href='approve_game.php?' style='color: green;'>Одобрить</a>
                        <a href='reject_game.php?' style='color: red;'>Отклонить</a>
                    </div>";
                }
            } else {
                echo "<p>Нет предложенных игр.</p>";
            }
            ?>
        </div>
    <?php } ?>
</body>

</html>
