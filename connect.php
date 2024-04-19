<?php 

$host = 'localhost';
$user = 'root';
$password = '';
$db = 'ussd';

$con = mysqli_connect($host,$user,$password,$db);

if (!$con) {
	# code...
	echo 'Database connection error'.mysqli_error($con);
}

?>