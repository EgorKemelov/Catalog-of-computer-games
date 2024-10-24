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
          <div class="frame">
                    <div class="game">
                        <img src="er.jpg" alt="" class="Eternal__Realms">
                        <P class="text">
                            Eternal Realms
                        </P>
                        <p class="description">
                            Explore a vast open world
                        </p>
                        
                    </div>
                    <div class="game 2">
                        <img src="bss.jpg" alt="" class="Bulletstorm">
                        <p class="text__2">
                            Bulletstorm
                        </p>
                        <p class="description__2">
                            Fast-paced multiplayer shooter
                        </p>
                    </div>
                    <div class="game 3">
                        <img src="mn.jpg" alt="" class="Moonli__Odyssey">
                        <p class="text__3">
                            Moonlit Odyssey
                        </p>
                        <p class="description__3">
                            Embark on a mystical journey
                        </p>
                    </div>
                    <div class="game 4">
                        <img src="ue.jpg" alt="" class="Urban__Empire">
                        <p class="text__4">
                            Urban Empire
                        </p>
                        <p class="description__4">
                            Build and manage your own city
                        </p>
                    </div>
                    
                
        </div>
        </div>
	
   
</body>

</html>

