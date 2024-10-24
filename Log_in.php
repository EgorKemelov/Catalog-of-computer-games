<?php
require_once ('config.php');
$login=mysqli_real_escape_string($connenct,trim($_POST['login']));
$pass = hash('sha512', trim($_POST['pass']));

if (empty($login) || empty ($pass))
{
    echo "Заполните все поля";
}
else {
    $sql = "SELECT * FROM users WHERE Nickname = '$login' AND Password = '$pass' limit 1";
    $result = $connenct->query($sql);
    if ($result->num_rows > 0)
    {
        while($row = $result->fetch_assoc()) {
            //echo "Добро пожаловать " . $row ['Nickname'];
            	session_start(['cookie_lifetime' => 7400]);
		$_SESSION['user'] = $login;
		 header("Location: index.php");
		
       		}
       		
      
       	
       
       
      
        
      
    } else {
        echo "Нет такого пользователя";
    }
}