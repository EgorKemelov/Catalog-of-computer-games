
<?php
 require_once ('config.php');
$connect = mysqli_connect($servername, $username, $password, $username) or die("Connection Error: " . mysqli_error($connect ));
$login=mysqli_real_escape_string($connect,trim($_POST['login']));
$pass = mysqli_real_escape_string($connect,trim($_POST['pass']));
$repeatpass = mysqli_real_escape_string($connect,trim($_POST['repeatpass']));
$email = mysqli_real_escape_string($connect,trim($_POST['email']));
$bdate = mysqli_real_escape_string($connect,trim($_POST['bdate']));


if (empty ($login) || empty ($pass) || empty($repeatpass) || empty($email) || empty($bdate)) {
    echo "Заполните все поля";
} else {
    if ($pass != $repeatpass) {
        echo "Пароли не совпадают";
        
    } else {$pass = hash('sha512', trim($_POST['pass']));
    
        $sql = "INSERT INTO users (Nickname,Password,Email,Birthdate) VALUES ('$login', '$pass','$email', '$bdate')"; 
        
       if ($connect -> query($sql) === TRUE) {
        echo "Успешная регистрация";
       }
       else {
        echo "Ошибка: " . $connect->error;
       }
    }
}






