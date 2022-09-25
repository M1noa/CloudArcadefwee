<?php
if(!file_exists($_SERVER["DOCUMENT_ROOT"]."/connect.php")){
	exit('CloudArcade not installed yet.');
}
date_default_timezone_set( "Australia/Sydney" );
require("connect.php");
define( "VERSION", "1.0.2" );
define( "CLASS_PATH", "classes" );
define( "ADMIN_PATH", "admin" );
define( "PRETTY_URL", true );
define( "URL_PROTOCOL", 'http://' );
define( "DOMAIN", URL_PROTOCOL . $_SERVER['SERVER_NAME'] . '/');
define( "ADMIN_DEMO", false );
require( CLASS_PATH . "/Page.php" );
require( CLASS_PATH . "/Category.php" );
require( CLASS_PATH . "/Game.php" );
require("site-settings.php");

function handleException( $exception ) {
	echo 'ERROR !; ';
	print_r($exception);
	error_log( $exception->getMessage() );
}

set_exception_handler( 'handleException' );
?>