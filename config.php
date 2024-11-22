<?php

    $servername = 'localhost';
    $username = 'kemelov.e.s';
    $password = '6354';
    $dbname = 'kemelov.e.s';

    $connect= mysqli_connect($servername, $username, $password, $dbname);
 if(!$connect){
    die("connection Failed". msqli_connect_error());
 } else {

 } ?>