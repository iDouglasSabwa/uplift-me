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
include 'keys.php';

//Assign session variable if text is not blank
if ($text !== "") {
		// code...
		session_start();
		$_SESSION['stext'] = $text;
		$stext = $_SESSION['stext'];
	}	

if ($text == "") {
	# Business logic for response level 1...
	$sql = "SELECT id,topic FROM topics ORDER BY id ASC";
	$sql = mysqli_query($con,$sql);

	if (mysqli_num_rows($sql)>1) {
		// code...
		$inslog = "INSERT INTO applogs(phone,session,topic,verse,date_created) VALUES ('$phoneNumber','$sessionId',NULL,NULL,'$idate')";
		$inslog = mysqli_query($con,$inslog);
	}

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

} elseif($text == $stext) {
	$stext = str_replace("98*","",$stext);//Remove extra characters if more is selected
	$stext = str_replace("0*","",$stext);//Remove extra characters if back is selected

	//Get a random verse id based on user input
	$maxsql = "SELECT id AS maxid FROM verses WHERE topic = '$stext' ORDER BY RAND() LIMIT 1";
	$maxsql = mysqli_query($con,$maxsql);

	if (mysqli_num_rows($maxsql)<1) {
		// If no verses available for that option...
		$response = "END Verses on option $stext will be available soon";
		
	} else {
		//If verse are available
		foreach ($maxsql as $key => $value) {
			$verse_id = $value['maxid'];

			//Get value of verse using the verse id
			$sql = "SELECT verses.id,verse,verse_text,topics.topic AS topic FROM verses INNER JOIN topics ON topics.id = verses.topic WHERE verses.id = '$verse_id';";
			$sql = mysqli_query($con,$sql);

			foreach ($sql as $key => $value) {
				# code...
				$id = $value['id'];
				$verse = $value['verse'];
				$verse_text = $value['verse_text'];
				//Truncated verse for screen display
				$trunc_verse = substr($verse_text,0,100).'...';
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
		            'Authorization: Bearer '.$token.''
		          ),
		        ));

		        $smsresponse = curl_exec($curl);
		        curl_close($curl);
		        // echo $response;   

		        //User display
				$response = "END Verse: $verse\n$trunc_verse\n";
		  
				}	
		}
}

		//Log results
		$inslog = "INSERT INTO applogs(phone,session,topic,verse,date_created) VALUES ('$phoneNumber','$sessionId','$stext','$verse_id','$idate')";
		$inslog = mysqli_query($con,$inslog);

		session_destroy();

	} else { 

	$response = "END Invalid Request";
}

echo $response;

; ?>