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
    			'cookie_lifetime' => 2*3600,
		]);
		if (isset($_SESSION['user'])){
			echo "Привет, ".$_SESSION['user'];
		}
?>
    <div class="countainer">
        <div class="header">
            <img src="logo.png" alt="" class="logo">
            
           <div class="buttons">
           <?php
    if ($_SESSION['user'] ==1)
           echo "<a href='logout.php' class='header__a'>Выйти</a>";?>

           <?php if (isset($_SESSION['user'] ==0))
           echo "<a href='Log_in.html' class='header__a'>Войти</a>
                <a href='Sign_up.html' class='header__a'>Зарегистрироваться</a>";
                ?>
                </div></div>
                
        </div>
    </div>
</body>

</html>

