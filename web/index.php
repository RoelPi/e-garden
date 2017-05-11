<?php
	include 'sqllogin.php';
	$token = (isset($_GET['token']) ? strip_tags($_GET['token']) : FALSE);
	// Valid token passed
	if ($token == 'roel') {
			$conn = new mysqli($sql_servername, $sql_username, $sql_password, $sql_db);
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		} 
		$conn -> set_charset("utf8mb4");
		$updatequery = $conn->prepare("INSERT INTO egardenstatus (TIMER,STATUS,METHOD) VALUES (?,?,?)");
		$dateTime = date('m/d/Y h:i:s', time());
		$stat = 1;
		$method = "manual";
		$updatequery->bind_param("sis", $dateTime,$stat,$method);
		$updatequery->execute();
		$updatequery->close();
		mysqli_close($conn);
		echo "Water requested!";
	} else {
		echo "Invalid token";
	}
?>