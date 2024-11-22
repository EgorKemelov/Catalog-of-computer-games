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
                    echo "<form action='Logout.php' method='post'><button type='submit' class='header__a'>Выйти</button></form>";
                    echo "<a href='profile.php' class='header__a'>Личный кабинет</a>"; // Ссылка на Личный кабинет
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
        <button type="submit">Поиск</button>
        <a href="index.php" style="margin-left: 10px;">Сбросить фильтрацию</a>

        <!-- Форма для фильтрации по жанрам -->
        <select name="genre_id" onchange="this.form.submit()">
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
                    <p class='text'>{$row['Game_name']}</p>
                    <p class='description'>{$row['Discription']}</p>
                    <p class='genres'>Жанры: {$row['genres']}</p> <!-- Отображение жанров -->
                    <a href='game.php?id={$row['id']}' class='page'>Страничка с игрой</a>";

                // Проверяем, является ли пользователь администратором
                if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] == 2) {
                    echo "<a href='edit_game.php?id={$row['id']}' class='edit-button' style='background: #13A3E8; color: white; text-decoration: none; position: relative; top: 8px; border-radius: 4px; padding: 3px'>Редактировать</a>
                    <a href='delete_game.php?id={$row['id']}' class='delete-button' style='background: red; color: white; text-decoration: none; margin-left: 10px; position: relative; top: 10px;' onclick='return confirm(\"Вы уверены, что хотите удалить эту игру?\");'>Удалить</a>";
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
        <a href='add_game.php' class='add-game-button' style='background: #13A3E8; color: white; text-decoration: none; position: relative; top: 8px; border-radius: 4px; padding: 3px;'>Добавить новую игру</a>
    <?php } ?>
</body>

</html>