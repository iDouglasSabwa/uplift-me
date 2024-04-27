<?php 
//Echo response back to the API
header('Content-type: text/plain');

//Read POST variables from the API
$sessionId = $_POST['sessionId'];
$networkCode = $_POST['networkCode'];
$serviceCode = $_POST['serviceCode'];
$phoneNumber = ltrim($_POST['phoneNumber']);
// $phoneNumber = ['phoneNumber'];
$text = $_POST['text'];
date_default_timezone_set("Africa/Nairobi");    
$idate =  date('Y-m-d H:i:s');

//Database connection file
include 'connect.php';


//Log results
	$inslog = "INSERT INTO applogs(phone,session,topic,verse,date_created) VALUES ('$phoneNumber','$sessionId',NULL,NULL,'$idate')";
	$inslog = mysqli_query($con,$inslog);

if ($text == "1") {
	# Business logic for response level 1...
	$sql = "SELECT id,topic FROM topics ORDER BY id ASC";
	$sql = mysqli_query($con,$sql);

	//Start screen
	$response = "CON Choose a topic\n";
	$number = 1;

	foreach ($sql as $key => $value) {
		# code...
		$topic_id = $value['id'];
		$topic = $value['topic'];

		//Screen options
		$response .= $number++ . ". $topic\n";

	}



} elseif($text =="") {
	# Business logic for response level 1...
	session_start();
	$_SESSION['stext'] = $text;
	$stext = $_SESSION['stext'];

	$maxsql = "SELECT id AS maxid FROM verses WHERE topic = 3 ORDER BY id DESC";
	$maxsql = mysqli_query($con,$maxsql);

	if (mysqli_num_rows($maxsql)<1) {
		// code...
		$response = "END Verses on option $stext will be available soon";
		
	} else {

	//Randomise verse

		foreach ($maxsql as $key => $random) {
			// code...
			$maxid = $random['maxid'];
			$randomNumbers = array();
			$randomNumbers[] = $maxid;
			$randomIndex = array_rand($randomNumbers);
			// $randomNumber = $maxsql[$randomIndex];

			// var_dump($randomIndex);
			print_r($randomNumbers);
			// $randomIndex = array_rand($random);
			// $randomNumber = $maxsql[$randomIndex];
			// var_dump($randomIndex);
		}

	// $max = mysqli_fetch_array($maxsql, MYSQLI_NUM);
	// printf ("%s\n", $max[1]);
	// var_dump($max);
	// $verse_id = $max[array_rand($max)];
	// $max = $max['maxid'];
	// $verse_id = mt_rand(1,$max);

	$sql = "SELECT verses.id,verse,verse_text,topics.topic AS topic FROM verses INNER JOIN topics ON topics.id = verses.topic WHERE verses.id = '$verse_id';";
	$sql = mysqli_query($con,$sql);

	foreach ($sql as $key => $value) {
		# code...
		$id = $value['id'];
		$verse = $value['verse'];
		$verse_text = $value['verse_text'];
		$topic = $value['topic'];

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
            "message": "Context: '.$topic.'\n\n'.$verse.'\n'.$verse_text.'\n",
            "phone": "'.$phoneNumber.'"
        }',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Bearer '
          ),
        ));

        $smsresponse = curl_exec($curl);
        curl_close($curl);
        // echo $response;   

        //User display
		$response = "END Verse: $verse\n$verse_text\n$verse_id\n$topic";
  
	}
}

		//Log results
		$inslog = "INSERT INTO applogs(phone,session,topic,verse,date_created) VALUES ('$phoneNumber','$sessionId','$text','$verse_id','$idate')";
		$inslog = mysqli_query($con,$inslog);

		session_destroy();

	} else { 

	$response = "END Invalid Request";
}

echo $response;

; ?>