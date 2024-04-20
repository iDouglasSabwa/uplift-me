<?php 
//Echo response back to the API
header('Content-type: text/plain');

//Read POST variables from the API
$sessionId = $_POST['sessionId'];
$networkCode = $_POST['networkCode'];
$serviceCode = $_POST['serviceCode'];
$phoneNumber = ltrim($_POST['phoneNumber']);
$text = $_POST['text'];
date_default_timezone_set("Africa/Nairobi");    
$idate =  date('Y-m-d H:i:s');

//Database connection file
include 'connect.php';

if ($text == "") {
	# This is the first request. Start the response with CON...
	$response = "CON How are you feeling?\n";
	$response .= "1. Positive\n";
	$response .= "2. Negative";

} elseif($text == "1") {
	# Business logic for response level 1...
	$sql = "SELECT id,mood_type FROM moods WHERE mood = 'Positive' ORDER BY mood_type";
	$sql = mysqli_query($con,$sql);

	foreach ($sql as $key => $value) {
		# code...
		$id = $value['id'];
		$mood_type = $value['mood_type'];
		#End execution
		$response = "CON How exactly?\n";
		$response .= "1. $mood_type\n";	
	}

	//Log results
		$inslog = "INSERT INTO applogs(phone,session,mood,mood_type,verse,date_created) VALUES ($phoneNumber,sessionId,'Positive',$mood_type,'','$idate')";

} elseif($text == "2") {
	# Business logic for response level 2...e
	$sql = "SELECT id,mood_type FROM moods WHERE mood = 'Negative' ORDER BY mood_type";
	$sql = mysqli_query($con,$sql);

	foreach ($sql as $key => $value) {
		# code...
		$id = $value['id'];
		$mood_type = $value['mood_type'];
		#End execution
		$response = "CON How exactly?\n";
		$response .= "1. $mood_type\n";	
		
	}

	//Log results
		$inslog = "INSERT INTO applogs(phone,session,mood,mood_type,verse,date_created) VALUES ($phoneNumber,sessionId,'Negative',$mood_type,'','$idate')";		

} else {
	$response = "END Invalid Request";
}

echo $response;

; ?>