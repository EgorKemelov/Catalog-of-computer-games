<?php

    $servername = 'localhost';
    $username = 'kemelov.e.s';
    $password = '6354';
    $dbname = 'kemelov.e.s';

    $connenct= mysqli_connect($servername, $username, $password, $dbname);
 if(!$connenct){
    die("connection Failed". msqli_connect_error());
 } else {

 } ?>