<?php
$servername = "localhost";
$username = "root";
$password = "";  
$dbname = "fizzliga_db";


$conn = mysqli_connect($servername, $username, $password, $dbname);

$sql="Select * From players";

$result=mysqli_query($conn,$sql);
?>
h