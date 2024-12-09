<?php
require_once('config.php');
session_start();
if ($_SESSION['user']['role'] != 2) {
    header("Location: index.php");
    exit();
}

$id = intval($_GET['id']);
$sql = "SELECT Game_name, Discription, Image FROM Game WHERE id = $id";
$result = $connect->query($sql);
if ($result->num_rows == 0) {
    echo "Игра не найдена.";
    exit();
}
$row = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $game_name = mysqli_real_escape_string($connect, trim($_POST['game_name']));
    $description = mysqli_real_escape_string($connect, trim($_POST['description']));
    $selectedGenres = $_POST['genre_ids']; // Массив выбранных жанров

    // Обработка загрузки нового изображения
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "images/";
        $image_name = $id . ".png";
        $target_file = $target_dir . $image_name;

        // Удаление старого изображения, если оно есть
        if (!empty($row['Image'])) {
            $old_image_path = $target_dir . $row['Image'];
            if (file_exists($old_image_path)) {
                unlink($old_image_path); // Удаляем старое изображение
            }
        }

        // Перемещение нового файла
        move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
        $update_sql = "UPDATE Game SET Game_name = '$game_name', Discription = '$description', Image = '$image_name' WHERE id = $id";
    } else {
        // Обновление без изменения изображения
        $update_sql = "UPDATE Game SET Game_name = '$game_name', Discription = '$description' WHERE id = $id";
    }

    // Сначала обновим информацию о игре
    if ($connect->query($update_sql) === TRUE) {
        // Удаляем старые записи жанров
        $delete_genre_sql = "DELETE FROM Game_jenre WHERE id_game = $id";
        $connect->query($delete_genre_sql);

        // Добавляем новые жанры
        if (!empty($selectedGenres)) {
            foreach ($selectedGenres as $genre_id) {
                $insert_genre_sql = "INSERT INTO Game_jenre (id_game, id_jenre) VALUES ($id, $genre_id)";
                $connect->query($insert_genre_sql);
            }
        }

        header("Location: index.php");
        exit();
    } else {
        echo "Ошибка обновления: " . $connect->error;
    }
}

// Получение жанров для выпадающего списка
$genre_sql = "SELECT id, name FROM Jenre";
$genre_result = $connect->query($genre_sql);
$genres = [];

// Получаем все жанры
while ($genre = $genre_result->fetch_assoc()) {
    $genres[] = $genre;
}

// Получаем жанры для текущей игры
$current_genres_sql = "SELECT gj.id_jenre FROM Game_jenre gj WHERE gj.id_game = $id";
$current_genres_result = $connect->query($current_genres_sql);
$current_genres = [];
while ($current_genre = $current_genres_result->fetch_assoc()) {
    $current_genres[] = $current_genre['id_jenre'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактировать игру</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #13A3E8;
        }
        form {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: auto;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"], textarea, select {
            width: calc(100% - 22px);
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            margin-bottom: 15px;
            font-size: 16px;
        }
        input[type="file"] {
            margin-bottom: 15px;
        }
        button {
            background-color: #13A3E8;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #0a7bb4; /* Темнее при наведении */
        }
        .current-image img {
            max-width: 100%;
            height: auto;
            border-radius: 5px; 
        }
        .cancel-link {
            display: inline-block;
            margin-top: 20px;
            text-align:center; 
            color:#13A3E8; 
          }
          .cancel-link:hover{
              text-decoration:none; 
              color:#0a7bb4; 
          }
          .button-container {
              display: flex; 
              justify-content: space-between; 
              align-items:center; 
          }
          .file-upload {
            position: relative;
            overflow: hidden;
            display: inline-block;
        }

        .file-upload input[type="file"] {
            position: absolute;
            top: 0;
            right: 0;
            opacity: 0; /* Скрываем оригинальный input */
            cursor: pointer; /* Курсор указывает на возможность взаимодействия */
            height: 100%;
            width: 100%;
        }

        .file-upload label {
            display: inline-block;
            padding: 10px 25px;
            background-color: #13A3E8; /* Цвет фона */
            color: white; /* Цвет текста */
            border-radius: 5px; /* Скругление углов */
            cursor: pointer; /* Курсор указывает на возможность взаимодействия */
            transition: background-color 0.3s; /* Плавный переход цвета фона */
        }

        .file-upload label:hover {
            background-color: #0a7bb4; /* Темнее при наведении */
        }
    </style>
</head>
<body>
    <h1>Редактировать игру</h1>
    <form method="post" enctype="multipart/form-data">
        <label for="game_name">Название игры:</label>
        <input type="text" id="game_name" name="game_name" value="<?php echo htmlspecialchars($row['Game_name']); ?>" required>

        <label for="description">Описание:</label>
        <textarea id="description" name="description" required><?php echo htmlspecialchars($row['Discription']); ?></textarea>

        <label for="genre_ids">Жанры:</label>
        <select id="genre_ids" name="genre_ids[]" multiple required>
            <?php foreach ($genres as $genre): ?>
                <option value="<?php echo htmlspecialchars($genre['id']); ?>" <?php if (in_array($genre['id'], $current_genres)) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($genre['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        
    <div class="file-upload">
        <input type="file" id="image" name="image" accept="image/png">
        <label for="image">Загрузить изображение</label>
    </div>
    
        <div class="button-container">
          <button type="submit">Сохранить изменения</button>
          <a class="cancel-link" href="index.php">Отмена</a>
      </div>
    </form>

    <?php if (!empty($row['Image'])): ?>
        <div class="current-image">
            <h2>Текущее изображение:</h2>
            <img src="images/<?php echo htmlspecialchars($row['Image']); ?>" alt="Текущее изображение">
            <a href="?id=<?php echo htmlspecialchars($id); ?>&action=delete_image" onclick="return confirm('Вы уверены, что хотите удалить изображение?');">Удалить изображение</a>
        </div>
    <?php endif; ?>
</body>
</html>
