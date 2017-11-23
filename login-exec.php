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
	
	$login = $_POST['login'];
	$password = $_POST['password'];

	//Input Validations
	if($login == '') {
		$errmsg_arr[] = 'Login faltando';
		$errflag = true;
	}
	if($password == '') {
		$errmsg_arr[] = 'Senha faltando';
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
	$qry="SELECT * FROM members WHERE login='$login' AND passwd='".md5($_POST['password'])."'";

	// Query
	if (!$mysqli->multi_query($qry)) {
    	die("Falha na query: (" . $mysqli->errno . ") " . $mysqli->error);
	}


	//Check whether the query was successful or not
	if ($result = $mysqli->store_result()) {
		if($result->num_rows == 1) {
			//Login Successful
			session_regenerate_id();

			$member = $result->fetch_all(MYSQLI_ASSOC);
			$_SESSION['SESS_MEMBER_ID'] = $member[0]['member_id'];
			$_SESSION['SESS_FIRST_NAME'] = $member[0]['firstname'];
			$_SESSION['SESS_LAST_NAME'] = $member[0]['lastname'];
			session_write_close();
			header("location: member-index.php");
			exit();
		}else {
			//Login failed
			header("location: login-failed.php");
			exit();
		}
	}else {
		die('Falha ao executar query: ' . mysql_error());
	}
?>