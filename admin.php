<?php

require( "config.php" );
require( "includes/commons.php" );
session_start();
$action = isset( $_GET['action'] ) ? $_GET['action'] : "";
$username = isset( $_SESSION['username'] ) ? $_SESSION['username'] : "";

if ( $action != "login" && $action != "logout" && !$username ) {
	login();
	exit;
}
switch ( $action ) {
	case 'login':
		login();
		break;
	case 'logout':
		logout();
		break;
	default:
		header( "Location: admin/dashboard.php" );
}
function login() {
	$results = array();
	if ( isset( $_POST['login'] ) ) {
		$data = get_user($_POST['username']);
		if ( $_POST['username'] == $data['username'] && $_POST['password'] == password_verify($_POST['password'], $data['password']) ) {
			$_SESSION['username'] = $data['username'];
			$ip_address = getIpAddr();
			$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql = "DELETE FROM loginlogs WHERE IpAddress = :ip_address";
			$st = $conn->prepare($sql);
			$st->bindValue(":ip_address", $ip_address, PDO::PARAM_STR);
			$st->execute();
			$conn = null;
			header( "Location: admin/dashboard.php" );
		} else {
			$results['error'] = "Incorrect username or password. ";
			require("admin/login.php" );
		}
	} else {
		require("admin/login.php" );
	}
}
function logout() {
	unset( $_SESSION['username'] );
	header( "Location: /" );
}

// Getting IP Address
function getIpAddr()
{
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		$ipAddr = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ipAddr = strtok($_SERVER['HTTP_X_FORWARDED_FOR'], ',');
	} else {
		$ipAddr = $_SERVER['REMOTE_ADDR'];
	}
	return $ipAddr;
}
?>