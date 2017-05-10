<?php
	ini_set('display_errors', 'On');
	include 'sqllogin.php';
	include 'setting.php';
	
	$token = (isset($_GET['token']) ? strip_tags($_GET['token']) : NULL);
	$sec = (isset($_GET['sec']) ? strip_tags($_GET['sec']) : NULL);
	$test = (isset($_GET['test']) ? strip_tags($_GET['test']) : NULL);
	
	if ($test <> 1) {
		// Valid token passed
		if ($token == $pass) {
			$conn = new mysqli($sql_servername, $sql_username, $sql_password, $sql_db);
			if ($conn->connect_error) {
				die("Connection failed: " . $conn->connect_error);
			} 
			$conn -> set_charset("utf8mb4");
			
			// Get latest request time
			$sql_query = "SELECT * FROM egardenstatus WHERE STATUS = 0 AND TOKEN = '" . $token . "' ORDER BY TIMER DESC LIMIT 1";
			$sql_result = $conn->query($sql_query);
			$sql_status = $sql_result->fetch_assoc();
			

			if ($last_call <= 10) {
				// Add row to ask for water
				$updatequery = $conn->prepare("INSERT INTO egardenstatus (TIMER,STATUS,METHOD,TOKEN,MOIST,TEMP,SEC) VALUES (?,?,?,?,?,?,?)");
				
				$dateTime = time();
				$stat = 1;
				$method = "manual";
				$moist = NULL;
				$temp = NULL;
				
				$updatequery->bind_param("iissiii", $dateTime,$stat,$method,$token,$moist,$temp,$sec);
				$updatequery->execute();
				$updatequery->close();
			
				// Everything is okay
				$status = [
					"status" => "water",
					"delay" => (string)(10 - (time() - $sql_status["TIMER"])),
					"sec" => (string)$sec
				];
				// Garden is not on
			} else {
				$status = [
					"status" => "nogarden"
				];
			}
		// No valid token specified
		} else {
			$status = ["status" => "notoken"];
		}
		mysqli_close($conn);
	} else {
		$status = [
			"status" => "water",
			"delay" => "8",
			"sec" => (string)$sec
		];
	}
	echo json_encode($status);
?>