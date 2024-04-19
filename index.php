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
	$response = "CON How are you feeling?\n";
	$response .= "1. Positive\n";
	$response .= "2. Negative";

} elseif($text == "1") {
	# Business logic for response level 1...
	$sql = "SELECT id,mood_type FROM moods WHERE mood = 'Positive'";
	$sql = mysqli_query($con,$sql);

	foreach ($sql as $key => $value) {
		# code...
		$id = $value['id'];
		$mood_type = $value['mood_type'];
		#End execution
		$response = "Positive";
	}

} elseif($text == "2") {
	# Business logic for response level 2...
	$sql = "SELECT id,mood_type FROM moods WHERE mood = 'Negative'";
	$sql = mysqli_query($con,$sql);

	foreach ($sql as $key => $value) {
		# code...
		$id = $value['id'];
		$mood_type = $value['mood_type'];
		#End execution
		$response = "Negative";
	}

} else {
	$response = "END Invalid Request";
}

echo $response;

; ?>