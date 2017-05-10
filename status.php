<?php
	ini_set('display_errors', 'On');
	include 'sqllogin.php';
	include 'setting.php';
	
	$token = (isset($_GET['token']) ? strip_tags($_GET['token']) : NULL);
	$moist = (isset($_GET['moist']) ? strip_tags($_GET['moist']) : NULL);
	$temp = (isset($_GET['temp']) ? strip_tags($_GET['temp']) : NULL);

	// Valid token passed
	if ($token == $pass) {
		$conn = new mysqli($sql_servername, $sql_username, $sql_password, $sql_db);
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		} 
		$conn -> set_charset("utf8mb4");
		
		// Entry into database
		$dateTime = time();
		$stat = 0;
		$method = "auto";
		$sec = NULL;
			
		// Check if last entry is a request for water		
		$sql_query = "SELECT * FROM egardenstatus WHERE TOKEN = '" . $token . "' ORDER BY TIMER DESC LIMIT 1";
		$sql_result = $conn->query($sql_query);
		$sql_status = $sql_result->fetch_assoc();
		
		// Status is 1 = water!
		if ($sql_status["STATUS"] == 1) {
			$status = [
				"status" => "water",
				"seconds" => $sql_status["SEC"]
			];
		// Status is 0 = no water!
		} else {
			$status = ["status" => "nowater"];
		}
		// No valid token passed = no water!
	} else {
		$status = ["status" => "notoken"];
	}
	
	$statusquery = $conn->prepare("INSERT INTO egardenstatus (TIMER,STATUS,METHOD,TOKEN,MOIST,TEMP,SEC) VALUES (?,?,?,?,?,?,?)");
	$statusquery->bind_param("iissiii", $dateTime,$stat,$method,$token,$moist,$temp,$sec);
	$statusquery->execute();
	$statusquery->close();
	
	echo json_encode($status);
	mysqli_close($conn);
?>
	