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
                    echo "<a href='profile.php' class='header__a'>Личный кабинет</a>";
                    echo "<form action='Logout.php' method='post'><button type='submit' class='header__a'>Выйти</button></form>";
                } else {
                    echo "<a href='Log_in.html' class='header__a'>Войти</a>";
                    echo "<a href='Sign_up.html' class='header__a'>Зарегистрироваться</a>";
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Форма для поиска игр -->
    <form method="GET" action="">
        <input type="text" name="search" placeholder="Поиск игр..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" required style="background: #13A3E8; border-radius: 20px; color: white;">
        <button type="submit" style="background: #13A3E8; border-radius: 20px; color: white;">Поиск</button>
        <a href="index.php" style="margin-left: 10px; background: #13A3E8; border-radius: 20px; color: black;">Сбросить фильтрацию</a>
        <select name="genre_id" onchange="this.form.submit()" style="background: #13A3E8; border-radius: 20px; color: black;">
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
        // Вывод игр
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

        if ($search) {
            $conditions[] = "g.Game_name LIKE ?";
            $params[] = '%' . $search . '%';
        }

        if ($selectedGenreId > 0) {
            $conditions[] = "gj.id_jenre = ?";
            $params[] = $selectedGenreId;
        }

        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " GROUP BY g.id";

        $stmt = $connect->prepare($sql);
        if ($params) {
            $types = '';
            foreach ($params as $param) {
                $types .= (is_int($param) ? 'i' : 's');
            }
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div id='g_{$row['id']}' class='game'>
                    <p>{$row['Game_name']}</p>
                    <p>{$row['Discription']}</p>
                    <p>Жанры: {$row['genres']}</p>
                </div>";
            }
        } else {
            echo "Нет доступных игр.";
        }

        $stmt->close();
        ?>
    </div>

    <!-- Форма для предложения игры -->
    <?php if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] == 1) { ?>
        <a href="suggest_game.php" style="background: #13A3E8; color: white; text-decoration: none; padding: 10px; border-radius: 10px;">Предложить игру</a>
    <?php } ?>

    <!-- Раздел для предложенных игр -->
    <?php if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] == 2) { ?>
        <h2>Предложенные игры</h2>
        <div class="suggested-games">
            <?php
            $suggestedGamesQuery = "SELECT * FROM Suggested_Game WHERE is_approved = 0";
            $suggestedGamesResult = $connect->query($suggestedGamesQuery);

            if ($suggestedGamesResult && $suggestedGamesResult->num_rows > 0) {
                while ($game = $suggestedGamesResult->fetch_assoc()) {
                    echo "<div class='suggested-game'>
                        <p>Название: {$game['Game_name']}</p>
                        <p>Описание: {$game['Discription']}</p>
                        <a href='approve_game.php?id={$game['id']}' style='color: green;'>Одобрить</a>
                        <a href='delete_suggested_game.php?id={$game['id']}' style='color: red;'>Отклонить</a>
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
