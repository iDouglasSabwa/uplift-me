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
	$sql = "SELECT id,mood_type FROM moods WHERE mood = 'Positive' ORDER BY mood_type ASC";
	$sql = mysqli_query($con,$sql);

	//Start screen
	$response = "CON How exactly?\n";
	$number = 1;

	foreach ($sql as $key => $value) {
		# code...
		$id = $value['id'];
		$mood_type = $value['mood_type'];

		//Screen options
		$response .= $number++ . ". $mood_type\n";	}

	//Log results
		$inslog = "INSERT INTO applogs(phone,session,mood,mood_type,verse,date_created) VALUES ('$phoneNumber','$sessionId','Positive','$mood_type','','$idate')";
		$inslog = mysqli_query($con,$inslog);


} elseif($text == "2") {
	# Business logic for response level 2...
	$sql = "SELECT id,mood_type FROM moods WHERE mood = 'Negative' ORDER BY mood_type ASC";
	$sql = mysqli_query($con,$sql);

	//Start screen
	$response = "CON How exactly?\n";
	$number = 1;

	foreach ($sql as $key => $value) {
		# code...
		$id = $value['id'];
		$mood_type = $value['mood_type'];

		//Screen options
		$response .= $number++ . ". $mood_type\n";		
	}

		//Log results
		$inslog = "INSERT INTO applogs(phone,session,mood,mood_type,verse,date_created) VALUES ('$phoneNumber','$sessionId','Negative','$mood_type','','$idate')";
		$inslog = mysqli_query($con,$inslog);
	

} elseif($text == "1*1") {
	# Business logic for response level 1*1...
	$maxsql = "SELECT id AS maxid FROM verses WHERE mood_type = '8' ORDER BY id DESC";
	$maxsql = mysqli_query($con,$maxsql);

	//Randomise verse
	$max = mysqli_fetch_array($maxsql);
	$max = $max['maxid'];
	$verse_id = mt_rand(1,$max);

	$sql = "SELECT verses.id,verse,verse_text,moods.mood_type AS mood_type FROM verses INNER JOIN moods ON moods.id = verses.mood_type WHERE verses.id = '$verse_id';";
	$sql = mysqli_query($con,$sql);

	foreach ($sql as $key => $value) {
		# code...
		$id = $value['id'];
		$verse = $value['verse'];
		$verse_text = $value['verse_text'];
		$mood_type = $value['mood_type'];

		//User display
		$response = "END Verse: $verse\n$verse_text\n$max";

		//Send text to the user
		$curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.mobilesasa.com/v1/send/message',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'{
            "senderID": "MOBILESASA",
            "message": "Mood: '.$mood_type.'\n'.$verse.'\n'.$verse_text.'",
            "phone": "'.$phoneNumber.'"
        }',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Bearer '
          ),
        ));

        curl_exec($curl);
        curl_close($curl);
        // echo $response;     
	}

		//Log results
		$inslog = "INSERT INTO applogs(phone,session,mood,mood_type,verse,date_created) VALUES ('$phoneNumber','$sessionId','Positive','8','$verse_id','$idate')";
		$inslog = mysqli_query($con,$inslog);


} else {
	$response = "END Invalid Request";
}

echo $response;

; ?>