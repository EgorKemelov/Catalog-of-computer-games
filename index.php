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
        <input type="text" name="search" placeholder="Поиск игр..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" required>
    
        <button type="submit" style='/*margin-left: 28px;*\ */
    /* padding: 16px 19px; */
    background: #13A3E8;
    border-radius: 20px;
    text-decoration: none;
    color: white;
    background: #13A3E8;'>Поиск</button>
        <a href="index.php" style="margin-left: 10px;
    /* margin-left: 28px; */
    /* padding: 16px 19px; */
    background: #13A3E8;
    border-radius: 20px;
    text-decoration: none;
    color: white;">Сбросить фильтрацию</a>

        <!-- Форма для фильтрации по жанрам -->
        <select name="genre_id" onchange="this.form.submit()" style='/*margin-left: 28px; *\*/
    /* padding: 16px 19px; */
    background: #13A3E8;
    border-radius: 20px;
    text-decoration: none;
    color: white;'>
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
                    <a href='game.php?id={$row['id']}' class='page' style='margin-left: 28px;
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: center;
    padding: 16px 19px;
    gap: 10px;
    background: #13A3E8;
    border-radius: 20px;
    text-decoration: none;
    color: white;
    width: 200px;
    position: relative;
    left: 20px;
    top: -40px;'>Страничка с игрой</a>";

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
                        <a href='approve_game.php?' style='color: green;'>К рассмотрению</a>
                        
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
