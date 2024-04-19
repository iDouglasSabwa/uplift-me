<?php 
//Echo response back to the API
header('Content-type: text/plain');

//Read POST variables from the API
$sessionId = $_POST['sessionId'];
$networkCode = $_POST['networkCode'];
$serviceCode = $_POST['serviceCode'];
$phoneNumber = ltrim($_POST['phoneNumber']);
$text = $_POST['text'];

//Database connection file
include 'connect.php';

if ($text == "") {
	# This is the first request. Start the response with CON...
	$response = "CON What would you want to do\n";
	$response .= "1. Check Status\n";
	$response .= "2. Make payments";

} elseif($text == "1") {
	# Business logic for response level 1...
	$no = 'KDH123';
	$sql = "SELECT name,status,vehicle FROM ussd WHERE vehicle = '$no'";
	$sql = mysqli_query($con,$sql);

	foreach ($sql as $key => $value) {
		# code...
		$name = $value['name'];
		$vehicle = $value['vehicle'];
		$status = $value['status'];
		#End execution
		$response = "END INSURANCE DETAILS\nCustomer Name: ".$name."\nVehicle status: ".$vehicle."\nStatus: ".$status."";
	}

} else {
	$response = "END Invalid Request";
}

echo $response;

; ?>