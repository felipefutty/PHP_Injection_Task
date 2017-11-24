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
	$fname = clean($_POST['fname'], $mysqli);
	$lname = clean($_POST['lname'], $mysqli);
	$login = clean($_POST['login'], $mysqli);
	$password = clean($_POST['password'], $mysqli);
	$cpassword = clean($_POST['cpassword'], $mysqli);
	
	//Input Validations
	if($fname == '') {
		$errmsg_arr[] = 'Primeiro nome faltando';
		$errflag = true;
	}
	if($lname == '') {
		$errmsg_arr[] = 'Ultimo nome faltando';
		$errflag = true;
	}
	if($login == '') {
		$errmsg_arr[] = 'Login faltando';
		$errflag = true;
	}
	if($password == '') {
		$errmsg_arr[] = 'Senha faltando';
		$errflag = true;
	}
	if($cpassword == '') {
		$errmsg_arr[] = 'Confirmacao de senha faltando';
		$errflag = true;
	}
	if( strcmp($password, $cpassword) != 0 ) {
		$errmsg_arr[] = 'Senhas nao batem';
		$errflag = true;
	}
	
	//Check for duplicate login ID
	if($login != '') {
		$qry = "SELECT * FROM members WHERE login='$login'";
		if ($result = $mysqli->query($qry)) {
			if($result->num_rows > 0) {
				$errmsg_arr[] = 'Login em uso, tente outro';
				$errflag = true;
			}
			//@mysql_free_result($result);
			$result->free();
		}
		else {
			die("Query falhou");
		}
	}
	
	//If there are input validations, redirect back to the registration form
	if($errflag) {
		$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
		session_write_close();
		header("location: register-form.php");
		exit();
	}

	//Create INSERT query
	$qry = "INSERT INTO members(firstname, lastname, login, passwd) VALUES('$fname','$lname','$login','".md5($_POST['password'])."')";
	//$result = @mysql_query($qry);
	
	//Check whether the query was successful or not
	if($result = $mysqli->query($qry)) {
		header("location: register-success.php");
		exit();
	}else {
		die("Query falhou");
	}
?>
