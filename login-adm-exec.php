<?php
	//Start session
	session_start();
	
	//Include database connection details
	require_once('config.php');
	
	//Array to store validation errors
	$errmsg_arr = array();
	
	//Validation error flag
	$errflag = false;
	
	//Connect to mysql server
	$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
	if ($mysqli->connect_errno) {
    	die("Falha ao conectar ao servidor MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
	}
	
	//Function to sanitize values received from the form. Prevents SQL injection
	function clean($str, $my) {
		$str = @trim($str);
		if(get_magic_quotes_gpc()) {
			$str = stripslashes($str);
		}
		return $my->real_escape_string($str);
	}
	
	//Sanitize the POST values
	$login = clean($_POST['login'], $mysqli);
	$password = clean($_POST['password'], $mysqli);
	
	//Input Validations
	if($login == '') {
		$errmsg_arr[] = 'Login ID missing';
		$errflag = true;
	}
	if($password == '') {
		$errmsg_arr[] = 'Password missing';
		$errflag = true;
	}
	
	//If there are input validations, redirect back to the login form
	if($errflag) {
		$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
		session_write_close();
		header("location: login-form.php");
		exit();
	}
	
	//Create query
	$qry="SELECT * FROM members,adm WHERE adm.member_id=members.member_id AND members.login='$login' AND members.passwd='".md5($_POST['password'])."'";

	// Query
	//if (!$mysqli->multi_query($qry)) {
    	//die("Falha na query: (" . $mysqli->errno . ") " . $mysqli->error);
	//}

	if ($result = $mysqli->query($qry)) {
		//echo $result->num_rows;
		if($result->num_rows == 1) {
			//Login Successful
			session_regenerate_id();

			$member = $result->fetch_all(MYSQLI_ASSOC);
			$_SESSION['SESS_MEMBER_ID'] = $member[0]['member_id'];
			$_SESSION['SESS_FIRST_NAME'] = $member[0]['firstname'];
			$_SESSION['SESS_LAST_NAME'] = $member[0]['lastname'];
			session_write_close();
			header("location: member-adm-index.php");
			exit();
		}else {
			//Login failed
			header("location: login-failed.php");
			exit();
		}
	}else {
		die("Query failed");
	}
?>
