<?php 

$host = 'localhost';
$user = 'root';
$password = '';
$db = 'upfilt-me';

$con = mysqli_connect($host,$user,$password,$db);

if (!$con) {
	# code...
	echo 'Database connection error'.mysqli_error($con);
}

?>