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

    if ($connect->query($update_sql) === TRUE) {
        header("Location: index.php");
        exit();
    } else {
        echo "Ошибка обновления: " . $connect->error;
    }
}

// Обработка удаления изображения
if (isset($_GET['action']) && $_GET['action'] == 'delete_image') {
    if (!empty($row['Image'])) {
        $old_image_path = "images/" . $row['Image'];
        if (file_exists($old_image_path)) {
            unlink($old_image_path); // Удаляем изображение с сервера
        }
        
        // Обновляем запись в базе данных, устанавливая Image в NULL
        $update_image_sql = "UPDATE Game SET Image = NULL WHERE id = $id";
        $connect->query($update_image_sql);
        header("Location: edit_game.php?id=$id"); // Перенаправляем обратно на страницу редактирования
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактировать игру</title>
</head>
<body>
    <h1>Редактировать игру</h1>
    <form method="post" enctype="multipart/form-data">
        <label for="game_name">Название игры:</label><br>
        <input type="text" id="game_name" name="game_name" value="<?php echo htmlspecialchars($row['Game_name']); ?>" required style='/*margin-left: 28px;*\ */
    border-radius: 20px;
    text-decoration: none;
    color: #000000;'><br><br>
        <label for="description">Описание:</label><br>
        <textarea id="description" name="description" required><?php echo htmlspecialchars($row['Discription']); ?></textarea><br><br>
        <label for="image">Загрузить изображение:</label><br>
        <input type="file" id="image" name="image" accept="image/png"><br><br>
        <button type="submit">Сохранить изменения</button>
    </form>

    <?php if (!empty($row['Image'])): ?>
        <h2>Текущее изображение:</h2>
        <img src="images/<?php echo htmlspecialchars($row['Image']); ?>" alt="Текущее изображение" width="100"><br>
        <a href="?id=<?php echo $id; ?>&action=delete_image" onclick="return confirm('Вы уверены, что хотите удалить изображение?');">Удалить изображение</a>
    <?php endif; ?>
    
    <a href="index.php">Отмена</a>
</body>
</html>
