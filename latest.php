<?php
	ini_set('display_errors', 'On');
	include 'sqllogin.php';
	include 'setting.php';
		$token = (isset($_GET['token']) ? strip_tags($_GET['token']) : NULL);
		
	// Valid token passed
	if ($token == $pass) {
		$conn = new mysqli($sql_servername, $sql_username, $sql_password, $sql_db);
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		} 
		$conn -> set_charset("utf8mb4");
		
		// Get latest request time
		$sql_query = "SELECT * FROM egardenstatus WHERE STATUS = 1 AND TOKEN = '" . $token . "' ORDER BY TIMER DESC LIMIT 1";
		$sql_result = $conn->query($sql_query);
		$sql_status = $sql_result->fetch_assoc();
		$status = [
			"date" => date("F j, Y",$sql_status["TIMER"]),
			"hour" => date("g:i a",$sql_status["TIMER"]),
			"diff" => (string)floor((time() - $sql_status["TIMER"])/60/60/24),
			"sec" => (string)$sql_status["SEC"]
		];

	} else {
		$status = [
			"date" => 'notoken'
		];
	}
	echo json_encode($status);
	mysqli_close($conn);
?>